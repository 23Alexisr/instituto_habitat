<?php

namespace App\Services;

use App\Models\Certificado;

class ServicioIndicadoresCalidad
{
    public function calcularPorcentajeError(): float
    {
        $total = Certificado::count();

        if ($total === 0) {
            return 0.0;
        }

        return round((Certificado::where('estado', 'anulado')->count() / $total) * 100, 1);
    }

    public function calcularPorcentajePendientes(): float
    {
        $total = Certificado::count();

        if ($total === 0) {
            return 0.0;
        }

        return round((Certificado::where('estado', 'pendiente')->count() / $total) * 100, 1);
    }

    public function totalEmitidos(): int
    {
        return Certificado::where('estado', 'emitido')->count();
    }
}
