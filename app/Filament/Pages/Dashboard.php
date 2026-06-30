<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Inicio';

    protected static ?string $title = 'Panel de Control';

    protected ?string $subheading = 'Resumen del sistema de certificación';

    public function getColumns(): int|string|array
    {
        return 3;
    }
}
