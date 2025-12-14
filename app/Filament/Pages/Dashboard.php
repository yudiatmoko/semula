<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TotalPenduduk;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\SemulaInfo;
use App\Filament\Widgets\GrafikPendukung;
use App\Filament\Widgets\GrafikPendukungPie;
use App\Filament\Widgets\TablePendukung;
use App\Filament\Widgets\TotalPendukung;
use App\Filament\Widgets\GrafikAktivitasPendukung;



class Dashboard extends BaseDashboard
{


    public function getWidgets(): array
    {
        return [
            SemulaInfo::class,
            AccountWidget::class,
            TotalPenduduk::class,
            TotalPendukung::class,
            GrafikPendukung::class,
            GrafikPendukungPie::class,
            GrafikAktivitasPendukung::class,
            TablePendukung::class,
        ];
    }

}
