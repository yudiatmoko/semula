<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Forms;
use App\Models\Pendukung;

class GrafikPendukung extends ChartWidget
{
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '300px';

    /**
     * 🔥 WAJIB AGAR ICON CORONG MUNCUL
     */
    protected function hasFiltersForm(): bool
    {
        return true;
    }

    protected function getFiltersFormColumns(): int
    {
        return 4;
    }

    /**
     * 🧠 JUDUL DINAMIS
     */
    public function getHeading(): string
    {
        $alamat = $this->filters['alamat'] ?? null;
        $jk     = $this->filters['jenis_kelamin'] ?? null;

        $judul = 'Grafik Pendukung';

        $judul .= $alamat
            ? " – {$alamat}"
            : ' – Semua Alamat';

        if ($jk === 'L') {
            $judul .= ' (Laki-laki)';
        } elseif ($jk === 'P') {
            $judul .= ' (Perempuan)';
        }

        return $judul;
    }

    /**
     * 📝 DESKRIPSI
     */
    public function getDescription(): ?string
    {
        $rt = $this->filters['rt'] ?? null;
        $rw = $this->filters['rw'] ?? null;

        if ($rt || $rw) {
            return 'Wilayah: '
                . ($rt ? "RT {$rt} " : '')
                . ($rw ? "RW {$rw}" : '');
        }

        return 'Menampilkan data pendukung per RT/RW';
    }

    /**
     * 🔍 FILTER FORM
     */
    protected function getFiltersForm(): ?Forms\Form
    {
        return $this->makeFiltersForm()->schema([
            Forms\Components\Select::make('alamat')
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
                ->live()
                ->afterStateUpdated(fn (callable $set) => [
                    $set('rt', null),
                    $set('rw', null),
                ])
                ->placeholder('Pilih Alamat'),

            Forms\Components\Select::make('rt')
                ->label('RT')
                ->options(fn (callable $get) =>
                    Pendukung::query()
                        ->when($get('alamat'), fn ($q) =>
                            $q->where('alamat', $get('alamat'))
                        )
                        ->select('rt')
                        ->distinct()
                        ->orderBy('rt')
                        ->pluck('rt', 'rt')
                        ->toArray()
                )
                ->live()
                ->placeholder('Semua RT'),

            Forms\Components\Select::make('rw')
                ->label('RW')
                ->options(fn (callable $get) =>
                    Pendukung::query()
                        ->when($get('alamat'), fn ($q) =>
                            $q->where('alamat', $get('alamat'))
                        )
                        ->when($get('rt'), fn ($q) =>
                            $q->where('rt', $get('rt'))
                        )
                        ->select('rw')
                        ->distinct()
                        ->orderBy('rw')
                        ->pluck('rw', 'rw')
                        ->toArray()
                )
                ->live()
                ->placeholder('Semua RW'),

            Forms\Components\Select::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->options([
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                ])
                ->placeholder('Semua'),
        ]);
    }

    /**
     * 📊 DATA GRAFIK + WARNA
     */
    protected function getData(): array
    {
        $query = Pendukung::query();

        foreach (['alamat', 'rt', 'rw', 'jenis_kelamin'] as $field) {
            if (!empty($this->filters[$field] ?? null)) {
                $query->where($field, $this->filters[$field]);
            }
        }

        $data = $query
            ->selectRaw("
                CONCAT(
                    IFNULL(alamat, ''),
                    ' | RT ', rt,
                    '/RW ', rw
                ) as label,
                COUNT(*) as total
            ")
            ->groupBy('alamat', 'rt', 'rw')
            ->orderBy('rw')
            ->orderBy('rt')
            ->pluck('total', 'label');

        $colors = $this->generateColors($data->count());

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendukung',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => $colors,   // 🔥 WARNA BAR
                    'borderColor' => $colors,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    /**
     * 🎨 GENERATOR WARNA DINAMIS
     */
    protected function generateColors(int $count): array
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 360 / max($count, 1));
            $colors[] = "hsl({$hue}, 70%, 55%)";
        }

        return $colors;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * ⚙️ OPTIONS
     */
    public function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],

                'datalabels' => [
                    'display' => true,
                    'color' => '#111827',
                    'anchor' => 'end',
                    'align' => 'top',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 12,
                    ],
                    'formatter' => fn ($value) => $value,
                ],
            ],

            'scales' => [
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
