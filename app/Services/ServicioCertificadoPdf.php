<?php

namespace App\Services;

use App\Models\Certificado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ServicioCertificadoPdf
{
    private function construirPdf(Certificado $certificado)
    {
        $certificado->loadMissing(['inscripcion.participante', 'inscripcion.curso']);

        return Pdf::loadView('certificados.plantilla', [
            'certificado'  => $certificado,
            'participante' => $certificado->inscripcion->participante,
            'curso'        => $certificado->inscripcion->curso,
        ])->setPaper('a4', 'landscape');
    }

    public function obtenerContenido(Certificado $certificado): string
    {
        return $this->construirPdf($certificado)->output();
    }

    public function descargar(Certificado $certificado): Response
    {
        $nombreArchivo = 'certificado-' . $certificado->codigo_verificacion . '.pdf';

        return $this->construirPdf($certificado)->download($nombreArchivo);
    }
}
