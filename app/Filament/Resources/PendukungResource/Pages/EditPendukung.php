<?php

namespace App\Filament\Resources\PendukungResource\Pages;

use App\Filament\Resources\PendukungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendukung extends EditRecord
{
    protected static string $resource = PendukungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
