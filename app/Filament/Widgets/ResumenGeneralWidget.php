<?php

namespace App\Filament\Widgets;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenGeneralWidget extends BaseWidget
{
    protected static ?int $sort = -5;

    protected function getStats(): array
    {
        $certificadosEsteMes = Certificado::where('estado', 'emitido')
            ->whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->count();

        $cursosActivos = Curso::where('fecha_fin', '>=', now())->count();

        return [
            Stat::make('Participantes', Participante::count())
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Cursos activos', $cursosActivos)
                ->description('Con fecha de fin vigente')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->icon('heroicon-o-academic-cap')
                ->color('warning'),

            Stat::make('Inscripciones', Inscripcion::count())
                ->description('Total acumulado')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Emitidos este mes', $certificadosEsteMes)
                ->description('Certificados del mes en curso')
                ->descriptionIcon('heroicon-m-document-check')
                ->icon('heroicon-o-document-check')
                ->color('success'),
        ];
    }
}
