<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Filament\Notifications\Notification;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if ($googleUser->email !== config('services.google.allowed_email')) {
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Email ini tidak diizinkan untuk mendaftar/login.')
                    ->danger()
                    ->send();
                return redirect('/admin/login');
            }

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                ]);
            }

            Auth::login($user);

            return redirect('/admin');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Login Gagal')
                ->body('Gagal login dengan Google.')
                ->danger()
                ->send();
            return redirect('/admin/login');
        }
    }
}
