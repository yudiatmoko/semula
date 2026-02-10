<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;
use Illuminate\Support\Facades\Schema;

class KoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('koordinators')->truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Factory::create('id_ID');

        $rtPerRw = [
            1 => 6,
            2 => 5,
            3 => 6,
            4 => 5,
            5 => 6,
            6 => 5,
            7 => 5,
            8 => 5,
        ];

        $data = [];

        foreach ($rtPerRw as $rw => $jumlahRt) {
            for ($rt = 1; $rt <= $jumlahRt; $rt++) {
                for ($k = 0; $k < 2; $k++) {
                    $data[] = [
                        'nama'       => $faker->name,
                        'rt'         => sprintf('%03d', $rt),
                        'rw'         => sprintf('%03d', $rw),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('koordinators')->insert($data);
    }
}
