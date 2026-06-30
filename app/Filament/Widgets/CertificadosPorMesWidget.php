<?php

namespace App\Filament\Widgets;

use App\Models\Certificado;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CertificadosPorMesWidget extends ChartWidget
{
    protected static ?int $sort = -3;

    protected static ?string $heading = 'Actividad mensual de certificados';

    protected static ?string $description = 'Emitidos y anulados en los últimos 6 meses';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $nombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $meses = collect(range(5, 0))->map(fn(int $i): Carbon => now()->subMonths($i));

        $labels = $meses->map(fn(Carbon $m): string =>
            $nombres[$m->month - 1] . ' ' . $m->year
        )->toArray();

        $emitidos = $meses->map(fn(Carbon $m): int =>
            Certificado::where('estado', 'emitido')
                ->whereMonth('fecha_emision', $m->month)
                ->whereYear('fecha_emision', $m->year)
                ->count()
        )->toArray();

        $anulados = $meses->map(fn(Carbon $m): int =>
            Certificado::where('estado', 'anulado')
                ->whereMonth('updated_at', $m->month)
                ->whereYear('updated_at', $m->year)
                ->count()
        )->toArray();

        return [
            'datasets' => [
                [
                    'label'                => 'Emitidos',
                    'data'                 => $emitidos,
                    'borderColor'          => '#8DC63F',
                    'backgroundColor'      => 'rgba(141, 198, 63, 0.12)',
                    'borderWidth'          => 2,
                    'pointBackgroundColor' => '#8DC63F',
                    'pointBorderColor'     => '#fff',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 5,
                    'pointHoverRadius'     => 7,
                    'tension'              => 0.4,
                    'fill'                 => true,
                ],
                [
                    'label'                => 'Anulados',
                    'data'                 => $anulados,
                    'borderColor'          => 'rgba(239, 68, 68, 0.85)',
                    'backgroundColor'      => 'rgba(239, 68, 68, 0.08)',
                    'borderWidth'          => 2,
                    'pointBackgroundColor' => 'rgba(239, 68, 68, 0.85)',
                    'pointBorderColor'     => '#fff',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 5,
                    'pointHoverRadius'     => 7,
                    'tension'              => 0.4,
                    'fill'                 => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'top',
                    'labels'   => ['usePointStyle' => true, 'padding' => 16],
                ],
                'tooltip' => [
                    'mode'      => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1, 'precision' => 0],
                    'grid'        => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
            'interaction' => [
                'mode'      => 'nearest',
                'axis'      => 'x',
                'intersect' => false,
            ],
        ];
    }
}
