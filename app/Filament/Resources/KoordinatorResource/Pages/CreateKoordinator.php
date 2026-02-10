<?php

namespace App\Filament\Resources\KoordinatorResource\Pages;

use App\Filament\Resources\KoordinatorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKoordinator extends CreateRecord
{
    protected static string $resource = KoordinatorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
