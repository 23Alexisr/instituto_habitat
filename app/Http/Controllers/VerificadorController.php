<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use Illuminate\Http\Request;

class VerificadorController extends Controller
{
    public function verificar(string $codigo)
    {
        $certificado = Certificado::with(['inscripcion.participante', 'inscripcion.curso'])
            ->where('codigo_verificacion', strtoupper($codigo))
            ->first();

        return view('verificador.show', compact('certificado', 'codigo'));
    }
}
