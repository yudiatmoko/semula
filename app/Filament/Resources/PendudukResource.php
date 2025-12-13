<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendudukResource\Pages;
use App\Models\Penduduk;
use App\Models\Pendukung;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;
    protected static ?string $slug = 'penduduk';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Master Penduduk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')->required()->unique(ignoreRecord: true)->maxLength(16),
                Forms\Components\TextInput::make('nama')->required(),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'laki-laki' => 'Laki-laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('alamat')->required()->columnSpanFull(),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('rt')->label('RT')->required(),
                    Forms\Components\TextInput::make('rw')->label('RW')->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deselectAllRecordsWhenFiltered(false)
            ->columns([
                Tables\Columns\TextColumn::make('nik')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin'),
                Tables\Columns\TextColumn::make('alamat')->limit(20),
                Tables\Columns\TextColumn::make('rt'),
                Tables\Columns\TextColumn::make('rw'),

                Tables\Columns\IconColumn::make('is_recruited')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->state(fn($record) => Pendukung::where('nik', $record->nik)->exists()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    BulkAction::make('salinKePendukung')
                        ->label('Salin & Set Koordinator')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tentukan Koordinator')
                        ->modalDescription('Masukkan nama koordinator untuk warga yang dipilih.')

                        ->form([
                            Forms\Components\TextInput::make('input_koordinator')
                                ->label('Nama Koordinator')
                                ->placeholder('Contoh: Pak Budi')
                                ->required()
                                ->autofocus(),
                        ])

                        ->action(function (Collection $records, array $data) {
                            $berhasil = 0;
                            $koordinator = $data['input_koordinator'];

                            foreach ($records as $warga) {
                                $exists = Pendukung::where('nik', $warga->nik)->exists();

                                if (!$exists) {
                                    Pendukung::create([
                                        'nik' => $warga->nik,
                                        'nama' => $warga->nama,
                                        'alamat' => $warga->alamat,
                                        'rt' => $warga->rt,
                                        'rw' => $warga->rw,
                                        'jenis_kelamin' => $warga->jenis_kelamin,

                                        'koordinator' => $koordinator,
                                    ]);
                                    $berhasil++;
                                }
                            }

                            if ($berhasil > 0) {
                                Notification::make()->title("Sukses salin {$berhasil} warga")->success()->send();
                            } else {
                                Notification::make()->title("Data sudah ada")->warning()->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenduduks::route('/'),
            'create' => Pages\CreatePenduduk::route('/create'),
            'edit' => Pages\EditPenduduk::route('/{record}/edit'),
        ];
    }
}