<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class PendukungLogSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('pendukung_logs')->truncate();
        Schema::enableForeignKeyConstraints();

        $totalPendukung = DB::table('pendukungs')->count();
        $days = 30;
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        // Bagi total pendukung ke 30 hari dengan distribusi random
        $dailyWeights = [];
        for ($i = 0; $i < $days; $i++) {
            // Beri bobot random, awal-awal lebih banyak
            $dailyWeights[] = rand(5, 30) + max(0, 15 - $i);
        }
        $totalWeight = array_sum($dailyWeights);

        $distributed = 0;
        $data = [];
        $current = $startDate->copy();

        for ($i = 0; $i < $days; $i++) {
            $dateStr = $current->toDateString();

            if ($i === $days - 1) {
                // Hari terakhir: sisanya
                $tambah = $totalPendukung - $distributed;
            } else {
                $tambah = (int) round(($dailyWeights[$i] / $totalWeight) * $totalPendukung);
                $tambah = max($tambah, 1);
            }

            // Log penambahan
            $data[] = [
                'tanggal'    => $dateStr,
                'perubahan'  => $tambah,
                'aksi'       => 'tambah',
                'admin_id'   => 1,
                'created_at' => $current->copy()->setTime(rand(8, 17), rand(0, 59)),
                'updated_at' => $current->copy()->setTime(rand(8, 17), rand(0, 59)),
            ];

            $distributed += $tambah;

            // Sesekali ada penghapusan (30% chance, kecuali hari pertama)
            if ($i > 0 && rand(1, 100) <= 30) {
                $hapus = rand(1, max(1, (int) ($tambah * 0.15)));
                $data[] = [
                    'tanggal'    => $dateStr,
                    'perubahan'  => -$hapus,
                    'aksi'       => 'hapus',
                    'admin_id'   => 1,
                    'created_at' => $current->copy()->setTime(rand(8, 17), rand(0, 59)),
                    'updated_at' => $current->copy()->setTime(rand(8, 17), rand(0, 59)),
                ];

                $distributed -= $hapus;
            }

            $current->addDay();
        }

        DB::table('pendukung_logs')->insert($data);
    }
}
