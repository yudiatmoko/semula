<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pendukung;
use Filament\Support\RawJs;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class GrafikPendukungPie extends ChartWidget
{
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '300px';

    /**
     * 🔥 STATE FILTER (WAJIB ADA)
     */
    public array $filters = [];

    /**
     * 🧠 JUDUL
     */
    public function getHeading(): string
    {
        return 'Persentase Pendukung per Alamat';
    }

    /**
     * 🔽 FILTER DROPDOWN (HEADER)
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter')
                ->icon('heroicon-o-funnel')
                ->form([
                    Select::make('alamat')
                        ->label('Alamat')
                        ->options(
                            Pendukung::query()
                                ->select('alamat')
                                ->distinct()
                                ->orderBy('alamat')
                                ->pluck('alamat', 'alamat')
                                ->toArray()
                        )
                        ->searchable()
                        ->placeholder('Semua Alamat'),

                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->placeholder('Semua'),
                ])
                ->action(function (array $data) {
                    // ✅ SIMPAN FILTER
                    $this->filters = array_filter($data);

                    // 🔥 PAKSA RELOAD CHART
                    $this->dispatch('refreshChart');
                })
                ->modalHeading('Filter Data')
                ->modalSubmitActionLabel('Terapkan'),
        ];
    }

    /**
     * 📊 DATA PIE → PERSENTASE PER ALAMAT (TOTAL = 100%)
     */
    protected function getData(): array
    {
        $query = Pendukung::query();

        foreach (['alamat', 'jenis_kelamin'] as $field) {
            if (!empty($this->filters[$field] ?? null)) {
                $query->where($field, $this->filters[$field]);
            }
        }

        // Hitung jumlah per alamat
        $counts = $query
            ->selectRaw('alamat as label, COUNT(*) as total')
            ->groupBy('alamat')
            ->orderBy('label')
            ->pluck('total', 'label');

        $grandTotal = $counts->sum();

        // Konversi ke persentase (TOTAL = 100)
        $percentages = $counts->map(fn ($value) =>
            $grandTotal > 0 ? round(($value / $grandTotal) * 100, 2) : 0
        );

        $colors = $this->generateColors($percentages->count());

        return [
            'datasets' => [
                [
                    'data' => $percentages->values()->toArray(),
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $percentages->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    /**
     * 🎨 WARNA UNIK SETIAP ALAMAT
     */
    protected function generateColors(int $count): array
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 360 / max($count, 1));
            $colors[] = "hsl({$hue}, 70%, 50%)";
        }

        return $colors;
    }

    /**
     * ⚙️ OPTIONS CHART
     */
    public function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],

                // 🔥 TAMPILKAN ANGKA PERSENTASE DI PIE
                'datalabels' => [
                    'color' => '#ffffff',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 13,
                    ],
                    'formatter' => RawJs::make("
                        function(value) {
                            return value + '%';
                        }
                    "),
                ],
            ],

            // ❌ HILANGKAN ANGKA SAMPING
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
