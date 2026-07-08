<?php

namespace Tests\Feature;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificadorControllerTest extends TestCase
{
    use RefreshDatabase;

    // Arma curso + participante + inscripcion + certificado, la cadena completa que la vista necesita
    private function crearCertificado(string $codigo, string $estado = 'emitido'): Certificado
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
            'codigo_verificacion' => $codigo,
            'estado' => $estado,
            'fecha_emision' => '2026-02-01',
        ]);
    }

    // Sin certificado con ese codigo, la vista debe avisar que no existe (no 404, la ruta siempre responde 200)
    public function test_codigo_inexistente_muestra_no_encontrado(): void
    {
        $response = $this->get('/verificar/NOEXISTE');

        $response->assertOk();
        $response->assertSee('no existe en el sistema', false);
    }

    // Caso feliz: certificado emitido y existente muestra nombre del participante y codigo
    public function test_codigo_valido_emitido_muestra_datos_del_certificado(): void
    {
        $this->crearCertificado('ABCD1234', 'emitido');

        $response = $this->get('/verificar/ABCD1234');

        $response->assertOk();
        $response->assertSee('Pedro Sanchez');
        $response->assertSee('ABCD1234');
    }

    // El controller hace strtoupper($codigo) antes de buscar, minusculas deben encontrar el mismo certificado
    public function test_busqueda_es_case_insensitive(): void
    {
        $this->crearCertificado('ABCD1234', 'emitido');

        $response = $this->get('/verificar/abcd1234');

        $response->assertOk();
        $response->assertSee('ABCD1234');
    }

    // Un certificado con estado "anulado" no debe mostrarse como valido, sino con aviso de baja
    public function test_certificado_anulado_muestra_aviso_de_baja(): void
    {
        $this->crearCertificado('ANULADO1', 'anulado');

        $response = $this->get('/verificar/ANULADO1');

        $response->assertOk();
        $response->assertSee('dado de baja', false);
    }

    // Un certificado "pendiente" no debe exponer datos personales, solo aviso de que esta en proceso

    public function test_certificado_pendiente_no_expone_datos_personales(): void
    {
        $this->crearCertificado('PEND1234', 'pendiente');

        $response = $this->get('/verificar/PEND1234');

        $response->assertOk();
        $response->assertSee('en proceso', false);
        $response->assertDontSee('Pedro Sanchez');
        $response->assertDontSee('12345678');
    }




}
