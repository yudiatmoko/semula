<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;
use Illuminate\Support\Facades\Schema;

class PendudukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('penduduks')->truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Factory::create('id_ID');
        $data = [];

        $rtPerRw = [
            1 => 6,  // RW 001: RT 001-006
            2 => 5,  // RW 002: RT 001-005
            3 => 6,  // RW 003: RT 001-006
            4 => 5,  // RW 004: RT 001-005
            5 => 6,  // RW 005: RT 001-006
            6 => 5,  // RW 006: RT 001-005
            7 => 5,  // RW 007: RT 001-005
            8 => 5,  // RW 008: RT 001-005
        ];

        $startNik = 3216000000000000;

        $totalData = 13250;
        $chunkSize = 500;

        for ($i = 1; $i <= $totalData; $i++) {

            $rw = $faker->numberBetween(1, 8);
            $rt = $faker->numberBetween(1, $rtPerRw[$rw]);

            $gender = $faker->randomElement(['L', 'P']);

            $nik = (string) ($startNik + $i);

            $data[] = [
                'nik'           => $nik,
                'nama'          => $gender == 'L' ? $faker->name('male') : $faker->name('female'),
                'alamat'        => $faker->address,
                'rt'            => sprintf('%03d', $rt),
                'rw'            => sprintf('%03d', $rw),
                'jenis_kelamin' => $gender,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (count($data) >= $chunkSize) {
                DB::table('penduduks')->insert($data);
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::table('penduduks')->insert($data);
        }
    }
}
