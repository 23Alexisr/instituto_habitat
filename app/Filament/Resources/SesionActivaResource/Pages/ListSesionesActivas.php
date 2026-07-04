<?php

namespace App\Filament\Resources\SesionActivaResource\Pages;

use App\Filament\Resources\SesionActivaResource;
use Filament\Resources\Pages\ListRecords;

class ListSesionesActivas extends ListRecords
{
    protected static string $resource = SesionActivaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
