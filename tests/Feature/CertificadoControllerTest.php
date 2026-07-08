<?php

namespace Tests\Feature;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificadoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function crearCertificado(string $estado): Certificado
    {
        $curso = Curso::create([
            'codigo' => 'CUR-1',
            'nombre' => 'curso de prueba',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'docente' => 'juan perez',
        ]);

        $participante = Participante::create([
            'nombre' => 'pedro sanchez',
            'dni' => '12345678',
            'correo' => 'pedro@example.com',
        ]);

        $inscripcion = Inscripcion::create([
            'curso_id' => $curso->id,
            'participante_id' => $participante->id,
            'fecha_inscripcion' => '2026-01-02',
        ]);

        return Certificado::create([
            'inscripcion_id' => $inscripcion->id,
            'estado' => $estado,
            'fecha_emision' => $estado === 'emitido' ? '2026-02-01' : null,
        ]);
    }

    // abort_unless($certificado->estaEmitido(), 403, ...) en el controller: pendiente/anulado no se descargan
    public function test_descarga_devuelve_403_si_certificado_no_esta_emitido(): void
    {
        $certificado = $this->crearCertificado('pendiente');

        $response = $this->actingAs(User::factory()->create())
            ->get(route('certificados.descargar', $certificado));

        $response->assertForbidden();
    }

    // Camino feliz: certificado emitido dispara ServicioCertificadoPdf real (QR + Pdf::loadView) y devuelve el binario
    public function test_descarga_devuelve_pdf_si_certificado_esta_emitido(): void
    {
        $certificado = $this->crearCertificado('emitido');

        $response = $this->actingAs(User::factory()->create())
            ->get(route('certificados.descargar', $certificado));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
