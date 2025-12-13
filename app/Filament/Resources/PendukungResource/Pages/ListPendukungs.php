<?php

namespace App\Filament\Resources\PendukungResource\Pages;

use App\Filament\Resources\PendukungResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendukungs extends ListRecords
{
    protected static string $resource = PendukungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
