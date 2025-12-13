<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PendukungLog;
use Illuminate\Support\Facades\DB;

class GrafikAktivitasPendukung extends ChartWidget
{
    protected static ?string $heading = 'Trend Naik Turun Pendukung';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $logs = PendukungLog::query()
            ->select(
                'tanggal',
                DB::raw('SUM(perubahan) as perubahan_harian')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $labels = [];
        $data = [];

        $total = 0;

        foreach ($logs as $log) {
            $total += $log->perubahan_harian;

            $labels[] = $log->tanggal->format('d M');
            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendukung',
                    'data' => $data,

                    // 🔥 INI YANG BIKIN GARIS KELIHATAN
                    'borderColor' => '#2563eb', // biru tebal
                    'backgroundColor' => 'rgba(37,99,235,0.15)',
                    'borderWidth' => 4,
                    'fill' => true,

                    // 🔥 TITIK NYA
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => '#2563eb',

                    // 🔥 GARIS HALUS
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
