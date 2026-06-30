<?php

namespace App\Filament\Widgets;

use App\Models\Certificado;
use Filament\Widgets\ChartWidget;

class EstadosCertificadosWidget extends ChartWidget
{
    protected static ?int $sort = -2;

    protected static ?string $heading = 'Distribución de estados';

    protected static ?string $description = 'Total acumulado de certificados';

    protected int|string|array $columnSpan = 1;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $emitidos   = Certificado::where('estado', 'emitido')->count();
        $pendientes = Certificado::where('estado', 'pendiente')->count();
        $anulados   = Certificado::where('estado', 'anulado')->count();

        return [
            'datasets' => [
                [
                    'data'            => [$emitidos, $pendientes, $anulados],
                    'backgroundColor' => [
                        'rgba(141, 198, 63, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                    ],
                    'borderColor' => [
                        '#8DC63F',
                        '#F59E0B',
                        '#EF4444',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['Emitidos', 'Pendientes', 'Anulados'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'bottom',
                    'labels'   => [
                        'usePointStyle' => true,
                        'padding'       => 16,
                        'font'          => ['size' => 12],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [],
                ],
            ],
            'cutout'       => '68%',
            'maintainAspectRatio' => false,
        ];
    }
}
