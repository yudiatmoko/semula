<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pendukung;
use App\Models\Penduduk;

class TotalPendukung extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return 'Ringkasan Pendukung';
    }

    protected function getStats(): array
    {
        $totalPenduduk = Penduduk::count();
        $totalPendukung = Pendukung::count();
        $laki = Pendukung::where('jenis_kelamin', 'L')->count();
        $perempuan = Pendukung::where('jenis_kelamin', 'P')->count();

        $persenTotal = $totalPenduduk > 0 ? round(($totalPendukung / $totalPenduduk) * 100, 1) : 0;
        $persenLaki = $totalPendukung > 0 ? round(($laki / $totalPendukung) * 100, 1) : 0;
        $persenPerempuan = $totalPendukung > 0 ? round(($perempuan / $totalPendukung) * 100, 1) : 0;

        return [
            Stat::make('Total Pendukung', $totalPendukung)
                ->icon('heroicon-o-document-check')
                ->description("{$persenTotal}% dari total penduduk")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('success'),

            Stat::make('Laki-laki', $laki)
                ->icon('heroicon-o-user')
                ->description("{$persenLaki}% dari pendukung")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('amber'),

            Stat::make('Perempuan', $perempuan)
                ->icon('heroicon-o-user')
                ->description("{$persenPerempuan}% dari pendukung")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('pink'),
        ];
    }

}
