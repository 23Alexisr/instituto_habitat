<?php

namespace App\Filament\Resources\InscripcionResource\Pages;

use App\Filament\Resources\InscripcionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInscripcion extends CreateRecord
{
    protected static string $resource = InscripcionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
