<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PendukungSeeder extends Seeder
{

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('pendukungs')->truncate();
        Schema::enableForeignKeyConstraints();

        // Ambil semua koordinator, grouped by RT/RW
        $koordinators = DB::table('koordinators')
            ->select('id', 'rt', 'rw')
            ->get()
            ->groupBy(fn($k) => $k->rt . '|' . $k->rw);

        // Ambil semua penduduk, grouped by RT/RW
        $penduduksByArea = DB::table('penduduks')
            ->select('nik', 'nama', 'alamat', 'rt', 'rw', 'jenis_kelamin')
            ->get()
            ->groupBy(fn($p) => $p->rt . '|' . $p->rw);

        $data = [];
        $chunkSize = 500;

        foreach ($penduduksByArea as $key => $penduduks) {
            // Random persentase pendukung per wilayah: 25% - 85%
            $persentase = rand(min: 25, max: 85) / 100;
            $jumlahPendukung = (int) ceil($penduduks->count() * $persentase);

            // Ambil koordinator untuk wilayah ini
            $areaKoordinators = $koordinators[$key] ?? collect();
            if ($areaKoordinators->isEmpty()) {
                continue;
            }
            $koordinatorIds = $areaKoordinators->pluck('id')->toArray();

            // Ambil random penduduk sebanyak jumlah yang ditentukan
            $selected = $penduduks->random(min($jumlahPendukung, $penduduks->count()));

            foreach ($selected as $penduduk) {
                // Assign ke salah satu koordinator di wilayah ini secara random
                $koordinatorId = $koordinatorIds[array_rand($koordinatorIds)];

                $data[] = [
                    'nik'            => $penduduk->nik,
                    'nama'           => $penduduk->nama,
                    'alamat'         => $penduduk->alamat,
                    'rt'             => $penduduk->rt,
                    'rw'             => $penduduk->rw,
                    'jenis_kelamin'  => $penduduk->jenis_kelamin,
                    'koordinator_id' => $koordinatorId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                if (count($data) >= $chunkSize) {
                    DB::table('pendukungs')->insert($data);
                    $data = [];
                }
            }
        }

        if (!empty($data)) {
            DB::table('pendukungs')->insert($data);
        }
    }
}
