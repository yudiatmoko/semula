<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pendukung;

class TotalPendukung extends StatsOverviewWidget
{
    /**
     * 🧠 JUDUL WIDGET
     */
    protected function getHeading(): ?string
    {
        return 'Ringkasan';
    }

    /**
     * 📊 STAT
     */
    protected function getStats(): array
{
    return [
        Stat::make('Total Pendukung', Pendukung::count())
            ->icon('heroicon-o-users')
            ->color('success'),

        Stat::make('Laki-laki', Pendukung::where('jenis_kelamin', 'Laki-laki')->count())
            ->icon('heroicon-o-user')
            ->color('primary'),

        Stat::make('Perempuan', Pendukung::where('jenis_kelamin', 'Perempuan')->count())
            ->icon('heroicon-o-user')
            ->color('pink'),
    ];
}

}
