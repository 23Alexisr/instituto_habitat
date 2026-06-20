<?php

namespace App\Filament\Widgets;

use App\Services\ServicioIndicadoresCalidad;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IndicadoresCalidadWidget extends BaseWidget
{
    protected static ?int $sort = -4;

    protected function getStats(): array
    {
        $servicio = app(ServicioIndicadoresCalidad::class);

        $porcentajeError      = $servicio->calcularPorcentajeError();
        $porcentajePendientes = $servicio->calcularPorcentajePendientes();
        $totalEmitidos        = $servicio->totalEmitidos();

        $colorError      = $porcentajeError > 10 ? 'danger' : ($porcentajeError > 0 ? 'warning' : 'success');
        $colorPendientes = $porcentajePendientes > 20 ? 'danger' : ($porcentajePendientes > 0 ? 'warning' : 'success');

        return [
            Stat::make('% Certificados con error', $porcentajeError . ' %')
                ->description('Certificados anulados sobre el total emitido')
                ->descriptionIcon('heroicon-m-x-circle')
                ->icon('heroicon-o-x-circle')
                ->color($colorError),

            Stat::make('% Certificados pendientes', $porcentajePendientes . ' %')
                ->description('Certificados pendientes sobre el total')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color($colorPendientes),

            Stat::make('Total emitidos', (string) $totalEmitidos)
                ->description('Certificados emitidos exitosamente')
                ->descriptionIcon('heroicon-m-document-check')
                ->icon('heroicon-o-document-check')
                ->color('success'),
        ];
    }
}
