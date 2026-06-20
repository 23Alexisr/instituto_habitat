<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Services\ServicioCertificadoPdf;
use Illuminate\Http\Response;

class CertificadoController extends Controller
{
    public function descargar(Certificado $certificado, ServicioCertificadoPdf $servicio): Response
    {
        abort_unless($certificado->estaEmitido(), 403, 'Solo se pueden descargar certificados con estado emitido.');

        return $servicio->descargar($certificado);
    }
}
