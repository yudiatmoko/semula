<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penduduk>
 */
class PendudukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $addresses;

        if (! $addresses) {
            for ($i = 0; $i < 15; $i++) {
                $addresses[] = fake()->address();
            }
        }

        $jk = fake()->randomElement(['L', 'P']);
        
        $genderCode = $jk == 'L' ? 'male' : 'female';

        return [
            'nik' => '32' . fake()->unique()->numerify('##############'), 
            
            'nama' => fake()->name($genderCode),
            
            'alamat' => fake()->randomElement($addresses),
            
            'rt' => str_pad(fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
            'rw' => str_pad(fake()->numberBetween(1, 5), 3, '0', STR_PAD_LEFT),
            
            'jenis_kelamin' => $jk,
        ];
    }
}