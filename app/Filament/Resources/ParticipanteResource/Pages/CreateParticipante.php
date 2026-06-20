<?php

namespace App\Filament\Resources\ParticipanteResource\Pages;

use App\Filament\Resources\ParticipanteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateParticipante extends CreateRecord
{
    protected static string $resource = ParticipanteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
