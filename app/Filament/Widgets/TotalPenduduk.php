<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Penduduk;

class TotalPenduduk extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return 'Ringkasan Penduduk';
    }

    protected function getStats(): array
    {
        $total = Penduduk::count();
        $laki = Penduduk::where('jenis_kelamin', 'Laki-laki')->count();
        $perempuan = Penduduk::where('jenis_kelamin', 'Perempuan')->count();

        $persenLaki = $total > 0 ? round(($laki / $total) * 100, 1) : 0;
        $persenPerempuan = $total > 0 ? round(($perempuan / $total) * 100, 1) : 0;

        return [
            Stat::make('Total Penduduk', $total)
                ->icon('heroicon-o-users'),

            Stat::make('Laki-laki', $laki)
                ->icon('heroicon-o-user')
                ->description("{$persenLaki}% dari total")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->descriptionColor('amber'),

            Stat::make('Perempuan', $perempuan)
                ->icon('heroicon-o-user')
                ->description("{$persenPerempuan}% dari total")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->descriptionColor('pink'),
        ];
    }

}
