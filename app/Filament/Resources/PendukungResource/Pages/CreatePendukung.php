<?php

namespace App\Filament\Resources\PendukungResource\Pages;

use App\Filament\Resources\PendukungResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePendukung extends CreateRecord
{
    protected static string $resource = PendukungResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
