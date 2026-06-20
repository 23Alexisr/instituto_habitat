<?php

namespace App\Filament\Resources\InscripcionResource\Pages;

use App\Filament\Resources\InscripcionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInscripcion extends EditRecord
{
    protected static string $resource = InscripcionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Eliminar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
