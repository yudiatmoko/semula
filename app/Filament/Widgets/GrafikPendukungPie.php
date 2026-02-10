<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penduduk;
use App\Models\Pendukung;

class GrafikPendukungPie extends ChartWidget
{
    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return 'Persentase Pendukung vs Non-Pendukung';
    }

    protected function getData(): array
    {
        $totalPenduduk = Penduduk::count();
        $totalPendukung = Pendukung::count();
        $nonPendukung = max($totalPenduduk - $totalPendukung, 0);

        return [
            'datasets' => [
                [
                    'data' => [$totalPendukung, $nonPendukung],
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(187, 182, 185, 0.65)',
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => [
                "Hak Pilih Pendukung",
                "Hak Pilih Non-Pendukung",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
