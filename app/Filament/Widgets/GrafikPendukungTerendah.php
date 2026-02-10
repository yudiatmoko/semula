<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penduduk;
use App\Models\Pendukung;

class GrafikPendukungTerendah extends ChartWidget
{
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '500px';
    public function getHeading(): string
    {

        $judul = '10 Wilayah dengan Pendukung Terendah';

        return $judul;
    }
    protected function getData(): array
    {
        $filterFields = ['alamat', 'rt', 'rw', 'jenis_kelamin'];

        $pendudukQuery = Penduduk::query();
        foreach ($filterFields as $field) {
            if (!empty($this->filters[$field] ?? null)) {
                $pendudukQuery->where($field, $this->filters[$field]);
            }
        }

        $pendudukSub = $pendudukQuery
            ->selectRaw('rt, rw, COUNT(*) as total_penduduk')
            ->groupBy('rt', 'rw');

        $pendukungQuery = Pendukung::query();
        foreach ($filterFields as $field) {
            if (!empty($this->filters[$field] ?? null)) {
                $pendukungQuery->where($field, $this->filters[$field]);
            }
        }

        $rows = $pendukungQuery
            ->joinSub($pendudukSub, 'penduduk_totals', function ($join) {
                $join
                    ->on('pendukungs.rt', '=', 'penduduk_totals.rt')
                    ->on('pendukungs.rw', '=', 'penduduk_totals.rw');
            })
            ->selectRaw(
                "pendukungs.rt,
                pendukungs.rw,
                COUNT(pendukungs.id) as total_pendukung,
                penduduk_totals.total_penduduk,
                ROUND((COUNT(pendukungs.id) / NULLIF(penduduk_totals.total_penduduk, 0)) * 100, 2) as persentase"
            )
            ->groupBy('pendukungs.rt', 'pendukungs.rw', 'penduduk_totals.total_penduduk')
            ->orderBy('total_pendukung', 'asc')
            ->limit(10)
            ->get();

        $labels = $rows
            ->map(fn($row) => "RT {$row->rt} / RW {$row->rw}")
            ->toArray();
        $totalPendukung = $rows->pluck('total_pendukung')->map(fn($value) => (int) $value)->toArray();
        $persentase = $rows->pluck('persentase')->map(fn($value) => (float) $value)->toArray();

        $colors = $this->generateColors(count($labels));

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendukung',
                    'data' => $totalPendukung,
                    'backgroundColor' => $colors,
                    'borderColor' => 'transparent',
                    'order' => 2,
                ],
                [
                    'label' => 'Persentase Pendukung',
                    'data' => $persentase,
                    'type' => 'line',
                    'yAxisID' => 'y1',
                    'order' => 1,
                    'showLine' => false,
                    'clip' => false,
                    'pointHitRadius' => 12,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                    'borderWidth' => 0,
                    'pointStyle' => 'rectRot',
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#f59e0b',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function generateColors(int $count): array
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 360 / max($count, 1));
            $colors[] = "hsla({$hue}, 70%, 55%, 0.7)";
        }
        return $colors;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'datalabels' => [
                    'display' => true,
                    'color' => '#111827',
                    'anchor' => 'end',
                    'align' => 'top',
                    'font' => ['weight' => 'bold', 'size' => 12],
                    'formatter' => 'function(value, context) { return context.dataset.yAxisID === "y1" ? value + "%" : value; }',
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                        'autoSkip' => false,
                    ],
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => [
                        'color' => '#e5e7eb',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Pendukung',
                    ],
                ],
                'y1' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'position' => 'right',
                    'border' => [
                        'color' => '#f59e0b',
                    ],
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
                        'text' => 'Persentase (%)',
                        'color' => '#f59e0b',
                    ],
                ],
            ],
        ];
    }
}