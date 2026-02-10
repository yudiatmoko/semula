<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PendukungLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GrafikAktivitasPendukung extends ChartWidget
{
    protected static ?string $heading = 'Trend Pertumbuhan Pendukung';

    protected int|string|array $columnSpan = '1';

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            'all' => 'Semua Data',
        ];
    }

    protected function getData(): array
    {
        $endDate = Carbon::today();

        if ($this->filter === 'all') {
            $firstLog = PendukungLog::query()->orderBy('tanggal')->value('tanggal');
            $startDate = $firstLog ? Carbon::parse($firstLog) : $endDate->copy()->subDays(29);
        } else {
            $days = max((int) $this->filter, 1);
            $startDate = $endDate->copy()->subDays($days - 1);
        }

        $perubahanMap = PendukungLog::query()
            ->select(
                'tanggal',
                DB::raw('SUM(perubahan) as perubahan_harian')
            )
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('perubahan_harian', 'tanggal')
            ->mapWithKeys(fn($value, $key) => [
                Carbon::parse($key)->toDateString() => (int) $value,
            ]);

        $initialTotal = (int) PendukungLog::query()
            ->whereDate('tanggal', '<', $startDate)
            ->sum('perubahan');

        $labels = [];
        $totals = [];
        $dailyChanges = [];
        $barColors = [];

        $total = $initialTotal;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dateKey = $current->toDateString();
            $perubahan = (int) ($perubahanMap[$dateKey] ?? 0);
            $total += $perubahan;

            $labels[] = $current->format('d M');
            $totals[] = $total;
            $dailyChanges[] = $perubahan;
            $barColors[] = $perubahan >= 0 ? 'rgba(245, 158, 11, 0.7)' : 'rgba(239, 68, 68, 0.7)';

            $current->addDay();
        }

        $showPoints = count($labels) <= 30;

        return [
            'datasets' => [
                [
                    'label' => 'Pertumbuhan Harian',
                    'data' => $dailyChanges,
                    'type' => 'bar',
                    'yAxisID' => 'y1',
                    'backgroundColor' => $barColors,
                    'borderColor' => 'transparent',
                    'barPercentage' => 0.6,
                    'categoryPercentage' => 0.8,
                    'order' => 2,
                ],
                [
                    'label' => 'Total Kumulatif',
                    'data' => $totals,
                    'type' => 'line',
                    'yAxisID' => 'y',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'pointRadius' => $showPoints ? 4 : 0,
                    'pointHoverRadius' => 6,
                    'tension' => 0.4,
                    'order' => 1,
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
            'maintainAspectRatio' => true,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'maxRotation' => 45,
                        'autoSkip' => true,
                        'maxTicksLimit' => 12,
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'beginAtZero' => true,
                    'position' => 'left',
                    'ticks' => ['precision' => 0],
                    'title' => [
                        'display' => true,
                        'text' => 'Total Kumulatif',
                    ],
                    'grid' => [
                        'color' => '#e5e7eb',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'beginAtZero' => true,
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                        'color' => '#f59e0b',
                    ],
                    'ticks' => [
                        'precision' => 0,
                        'color' => '#f59e0b',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Pertumbuhan Harian',
                        'color' => '#f59e0b',
                    ],
                ],
            ],
        ];
    }
}