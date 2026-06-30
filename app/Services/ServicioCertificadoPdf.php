<?php

namespace App\Services;

use App\Models\Certificado;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;

class ServicioCertificadoPdf
{
    private function construirPdf(Certificado $certificado)
    {
        $certificado->loadMissing(['inscripcion.participante', 'inscripcion.curso']);

        $urlVerificacion = route('certificados.verificar', $certificado->codigo_verificacion);

        $qrCode = QrCode::create($urlVerificacion)->setSize(120)->setMargin(2);
        $qrPng  = (new PngWriter())->write($qrCode)->getString();
        $qrBase64 = base64_encode($qrPng);

        return Pdf::loadView('certificados.plantilla', [
            'certificado'  => $certificado,
            'participante' => $certificado->inscripcion->participante,
            'curso'        => $certificado->inscripcion->curso,
            'qrBase64'     => $qrBase64,
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
