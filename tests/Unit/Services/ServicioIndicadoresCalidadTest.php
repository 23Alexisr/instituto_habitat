<?php

namespace Tests\Unit\Services;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use App\Services\ServicioIndicadoresCalidad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicioIndicadoresCalidadTest extends TestCase
{
    use RefreshDatabase;

    // uniqid()/random_int() evitan choques de unique (codigo, dni, correo) al crear varios certificados por test
    private function crearCertificado(string $estado): Certificado
    {
        $curso = Curso::create([
            'codigo' => 'CUR-' . uniqid(),
            'nombre' => 'curso de prueba',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'docente' => 'juan perez',
        ]);

        $participante = Participante::create([
            'nombre' => 'pedro sanchez ' . uniqid(),
            'dni' => (string) random_int(10000000, 99999999),
            'correo' => uniqid() . '@example.com',
        ]);

        $inscripcion = Inscripcion::create([
            'curso_id' => $curso->id,
            'participante_id' => $participante->id,
            'fecha_inscripcion' => '2026-01-02',
        ]);

        return Certificado::create([
            'inscripcion_id' => $inscripcion->id,
            'estado' => $estado,
        ]);
    }

    // Division por cero en Certificado::count()=0 debe devolver 0.0 en vez de NAN/excepcion
    public function test_sin_certificados_devuelve_cero(): void
    {
        $servicio = new ServicioIndicadoresCalidad();

        $this->assertSame(0.0, $servicio->calcularPorcentajeError());
        $this->assertSame(0.0, $servicio->calcularPorcentajePendientes());
        $this->assertSame(0, $servicio->totalEmitidos());
    }

    // 4 certificados (2 emitidos, 1 anulado, 1 pendiente): error y pendientes en 25%, emitidos en 2
    public function test_calcula_porcentajes_y_totales_correctamente(): void
    {
        $this->crearCertificado('emitido');
        $this->crearCertificado('emitido');
        $this->crearCertificado('anulado');
        $this->crearCertificado('pendiente');

        $servicio = new ServicioIndicadoresCalidad();

        $this->assertSame(25.0, $servicio->calcularPorcentajeError());
        $this->assertSame(25.0, $servicio->calcularPorcentajePendientes());
        $this->assertSame(2, $servicio->totalEmitidos());
    }
}
