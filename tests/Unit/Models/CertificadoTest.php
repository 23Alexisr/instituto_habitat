<?php

namespace Tests\Unit\Models;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificadoTest extends TestCase
{
    use RefreshDatabase;

    // Certificado exige inscripcion_id, arma la cadena curso + participante + inscripcion
    private function crearInscripcion(): Inscripcion
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

        return Inscripcion::create([
            'curso_id' => $curso->id,
            'participante_id' => $participante->id,
            'fecha_inscripcion' => '2026-01-02',
        ]);
    }

    // El boot() del modelo genera codigo_verificacion solo si viene vacio al crear
    public function test_genera_codigo_verificacion_automatico_al_crear(): void
    {
        $certificado = Certificado::create([
            'inscripcion_id' => $this->crearInscripcion()->id,
            'estado' => 'pendiente',
        ]);

        $this->assertNotEmpty($certificado->codigo_verificacion);
        $this->assertSame(8, strlen($certificado->codigo_verificacion));
    }

    // Caso reemision: si ya se pasa codigo_verificacion, el boot() no debe pisarlo
    public function test_no_sobrescribe_codigo_verificacion_si_ya_viene_informado(): void
    {
        $certificado = Certificado::create([
            'inscripcion_id' => $this->crearInscripcion()->id,
            'codigo_verificacion' => 'FIJO1234',
            'estado' => 'pendiente',
        ]);

        $this->assertSame('FIJO1234', $certificado->codigo_verificacion);
    }

    // generarCodigoVerificacion() reintenta en un while contra la DB hasta que sea unico, generamos varios de seguido
    public function test_codigo_verificacion_generado_es_unico(): void
    {
        $inscripcion = $this->crearInscripcion();

        $codigos = [];
        for ($i = 0; $i < 5; $i++) {
            $codigos[] = Certificado::generarCodigoVerificacion();
        }

        $this->assertCount(5, array_unique($codigos));
    }

    // estaEmitido/estaAnulado/estaPendiente deben ser mutuamente excluyentes segun el campo "estado"
    public function test_metodos_de_estado(): void
    {
        $inscripcion = $this->crearInscripcion();

        $emitido = Certificado::create(['inscripcion_id' => $inscripcion->id, 'estado' => 'emitido']);
        $this->assertTrue($emitido->estaEmitido());
        $this->assertFalse($emitido->estaAnulado());
        $this->assertFalse($emitido->estaPendiente());
    }
}
