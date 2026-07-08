<?php

namespace Tests\Unit\Models;

use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoTest extends TestCase
{
    use RefreshDatabase;

    // El mutator de "codigo" debe normalizar mayusculas y recortar espacios antes de guardar
    public function test_codigo_se_guarda_en_mayusculas_y_sin_espacios(): void
    {
        $curso = Curso::create([
            'codigo' => '  abc-123  ',
            'nombre' => 'curso de prueba',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'docente' => 'juan perez',
        ]);

        $this->assertSame('ABC-123', $curso->codigo);
    }

    // Los mutators de "nombre" y "docente" deben pasar el texto a formato titulo
    public function test_nombre_y_docente_se_guardan_en_formato_titulo(): void
    {
        $curso = Curso::create([
            'codigo' => 'CUR-1',
            'nombre' => '  gestión de proyectos  ',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'docente' => '  maría lópez  ',
        ]);

        $this->assertSame('Gestión De Proyectos', $curso->nombre);
        $this->assertSame('María López', $curso->docente);
    }

    // fecha_inicio y fecha_fin estan declaradas en $casts como "date", deben llegar como Carbon
    public function test_fechas_se_castean_a_carbon(): void
    {
        $curso = Curso::create([
            'codigo' => 'CUR-2',
            'nombre' => 'curso x',
            'fecha_inicio' => '2026-02-01',
            'fecha_fin' => '2026-02-28',
            'docente' => 'ana ruiz',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $curso->fecha_inicio);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $curso->fecha_fin);
    }

    // Un curso creado sin inscripciones asociadas debe poder acumularlas via hasMany
    public function test_relacion_inscripciones(): void
    {
        $curso = Curso::create([
            'codigo' => 'CUR-3',
            'nombre' => 'curso y',
            'fecha_inicio' => '2026-03-01',
            'fecha_fin' => '2026-03-31',
            'docente' => 'luis diaz',
        ]);

        $participante = Participante::create([
            'nombre' => 'pedro sanchez',
            'dni' => '12345678',
            'correo' => 'pedro@example.com',
        ]);

        Inscripcion::create([
            'curso_id' => $curso->id,
            'participante_id' => $participante->id,
            'fecha_inscripcion' => '2026-03-02',
        ]);

        $this->assertCount(1, $curso->inscripciones);
        $this->assertInstanceOf(Inscripcion::class, $curso->inscripciones->first());
    }
}
