<?php

namespace Database\Seeders;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosPreTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Certificado::truncate();
        Inscripcion::truncate();
        Participante::truncate();
        Curso::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── CURSOS ────────────────────────────────────────────────────────────
        $contabilidad   = Curso::create(['codigo' => 'CONT-2025', 'nombre' => 'Contabilidad Básica',         'docente' => 'Dra. María Pérez Alvarado',   'fecha_inicio' => '2025-03-01', 'fecha_fin' => '2025-05-30']);
        $administracion = Curso::create(['codigo' => 'ADM-2025',  'nombre' => 'Administración de Empresas',  'docente' => 'Prof. Luis Vargas Herrera',    'fecha_inicio' => '2025-04-01', 'fecha_fin' => '2025-06-30']);
        $marketing      = Curso::create(['codigo' => 'MARK-2025', 'nombre' => 'Marketing Digital',           'docente' => 'Lic. Carmen Flores Quispe',    'fecha_inicio' => '2025-05-01', 'fecha_fin' => '2025-07-31']);
        $excel          = Curso::create(['codigo' => 'EXCL-2026', 'nombre' => 'Excel Avanzado',              'docente' => 'Prof. Roberto Salas Díaz',     'fecha_inicio' => '2026-01-15', 'fecha_fin' => '2026-03-15']);
        $calidad        = Curso::create(['codigo' => 'SOFT-2026', 'nombre' => 'Calidad de Software',         'docente' => 'Dr. Ana Torres Espinoza',      'fecha_inicio' => '2026-04-01', 'fecha_fin' => '2026-06-30']);

        // ── PARTICIPANTES ─────────────────────────────────────────────────────
        $juan     = Participante::create(['nombre' => 'Juan Carlos Ríos Mendoza',      'dni' => '45678901', 'correo' => 'jcrios@example.com']);
        $ana      = Participante::create(['nombre' => 'Ana Sofía Torres Vega',         'dni' => '72345678', 'correo' => 'atorres@example.com']);
        $carlos   = Participante::create(['nombre' => 'Carlos Eduardo Mendoza López',  'dni' => '56789012', 'correo' => 'cmendoza@example.com']);
        $lucia    = Participante::create(['nombre' => 'Lucía Patricia Flores Huamán',  'dni' => '83456789', 'correo' => 'lflores@example.com']);
        $manuel   = Participante::create(['nombre' => 'Manuel José Ramírez Jiménez',   'dni' => '26654651', 'correo' => 'mramirez@example.com']);
        $diego    = Participante::create(['nombre' => 'Diego Alejandro Soto Ponce',    'dni' => '67890123', 'correo' => 'dsoto@example.com']);
        $valeria  = Participante::create(['nombre' => 'Valeria Consuelo Ríos Castro',  'dni' => '91234567', 'correo' => 'vrios@example.com']);
        $roberto  = Participante::create(['nombre' => 'Roberto Carlos Paredes Ruiz',   'dni' => '34567890', 'correo' => 'rparedes@example.com']);
        $milagros = Participante::create(['nombre' => 'Milagros Beatriz Cano Silva',   'dni' => '78901234', 'correo' => 'mcano@example.com']);
        $fernando = Participante::create(['nombre' => 'Fernando José Gutiérrez Mora',  'dni' => '45678902', 'correo' => 'fgutierrez@example.com']);

        // ── INSCRIPCIONES + CERTIFICADOS ─────────────────────────────────────
        // Distribución final: 11 emitidos / 4 anulados / 5 pendientes = 20 total
        // % error     = 4/20 = 20.0 %
        // % pendientes = 5/20 = 25.0 %

        // ── CONT-2025: Contabilidad Básica ────────────────────────────────────

        // 1. Juan Carlos — emitido correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($juan,   $contabilidad, '2025-03-01')->id, 'estado' => 'emitido',   'fecha_emision' => '2025-06-02']);

        // 2. Ana Sofía — emitida correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($ana,    $contabilidad, '2025-03-01')->id, 'estado' => 'emitido',   'fecha_emision' => '2025-06-02']);

        // 3 + 4. Carlos Eduardo — ANULADO por error de nombre → reemitido
        //        Error típico: nombre abreviado al copiar desde lista en papel
        $iCarlosCont = $this->inscribir($carlos, $contabilidad, '2025-03-02');
        $anulado1 = Certificado::create([
            'inscripcion_id'   => $iCarlosCont->id,
            'estado'           => 'anulado',
            'fecha_emision'    => '2025-06-03',
            'motivo_anulacion' => 'Error en nombre del participante ingresado manualmente: se registró "Carlos Mendoza" en lugar de "Carlos Eduardo Mendoza López".',
        ]);
        Certificado::create([                                           // reemisión → emitido
            'inscripcion_id'  => $iCarlosCont->id,
            'estado'          => 'emitido',
            'fecha_emision'   => '2025-06-05',
            'reemitido_de_id' => $anulado1->id,
        ]);

        // 5. Lucía — PENDIENTE (inscripción aprobada, certificado nunca generado)
        Certificado::create(['inscripcion_id' => $this->inscribir($lucia,  $contabilidad, '2025-03-02')->id, 'estado' => 'pendiente', 'fecha_emision' => null]);

        // ── ADM-2025: Administración de Empresas ──────────────────────────────

        // 6. Manuel — emitido correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($manuel,  $administracion, '2025-04-01')->id, 'estado' => 'emitido', 'fecha_emision' => '2025-07-15']);

        // 7. Diego — emitido correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($diego,   $administracion, '2025-04-01')->id, 'estado' => 'emitido', 'fecha_emision' => '2025-07-15']);

        // 8. Valeria — ANULADO por fecha incorrecta (plantilla reutilizada del año anterior)
        Certificado::create([
            'inscripcion_id'   => $this->inscribir($valeria, $administracion, '2025-04-02')->id,
            'estado'           => 'anulado',
            'fecha_emision'    => '2025-07-16',
            'motivo_anulacion' => 'Fecha de emisión incorrecta: plantilla Word reutilizada del ciclo 2024 sin actualizar el año, certificado impreso y entregado con fecha errónea.',
        ]);

        // 9. Roberto — PENDIENTE
        Certificado::create(['inscripcion_id' => $this->inscribir($roberto,  $administracion, '2025-04-02')->id, 'estado' => 'pendiente', 'fecha_emision' => null]);

        // ── MARK-2025: Marketing Digital ──────────────────────────────────────

        // 10. Milagros — emitida correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($milagros, $marketing, '2025-05-01')->id, 'estado' => 'emitido', 'fecha_emision' => '2025-08-10']);

        // 11. Fernando — emitido correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($fernando, $marketing, '2025-05-01')->id, 'estado' => 'emitido', 'fecha_emision' => '2025-08-10']);

        // 12. Diego (2.° curso) — PENDIENTE
        Certificado::create(['inscripcion_id' => $this->inscribir($diego,    $marketing, '2025-05-02')->id, 'estado' => 'pendiente', 'fecha_emision' => null]);

        // 13. Roberto (2.° curso) — emitido correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($roberto,  $marketing, '2025-05-02')->id, 'estado' => 'emitido', 'fecha_emision' => '2025-08-12']);

        // ── EXCL-2026: Excel Avanzado ─────────────────────────────────────────

        // 14. Valeria (2.° curso) — ANULADO por datos del docente incorrectos
        Certificado::create([
            'inscripcion_id'   => $this->inscribir($valeria,  $excel, '2026-01-15')->id,
            'estado'           => 'anulado',
            'fecha_emision'    => '2026-03-20',
            'motivo_anulacion' => 'Nombre del docente incorrecto: se copió "Dr. Ana Torres Espinoza" (docente de otro curso) por reutilización de plantilla sin revisión previa.',
        ]);

        // 15. Milagros (2.° curso) — PENDIENTE
        Certificado::create(['inscripcion_id' => $this->inscribir($milagros, $excel, '2026-01-15')->id, 'estado' => 'pendiente', 'fecha_emision' => null]);

        // 16. Ana (2.° curso) — emitida correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($ana,      $excel, '2026-01-16')->id, 'estado' => 'emitido', 'fecha_emision' => '2026-03-20']);

        // ── SOFT-2026: Calidad de Software ────────────────────────────────────

        // 17. Juan Carlos — emitido correctamente
        Certificado::create(['inscripcion_id' => $this->inscribir($juan,     $calidad, '2026-04-01')->id, 'estado' => 'emitido', 'fecha_emision' => '2026-06-20']);

        // 18 + 19. Manuel (2.° curso) — ANULADO por duplicado → reemitido
        //          Error típico: sin registro centralizado se emite dos veces el mismo certificado
        $iManuelCalidad = $this->inscribir($manuel, $calidad, '2026-04-01');
        $anulado2 = Certificado::create([
            'inscripcion_id'   => $iManuelCalidad->id,
            'estado'           => 'anulado',
            'fecha_emision'    => '2026-06-18',
            'motivo_anulacion' => 'Certificado duplicado: emitido dos veces por error operativo al no existir registro centralizado que impida la emisión múltiple para el mismo participante.',
        ]);
        Certificado::create([                                           // reemisión → emitido
            'inscripcion_id'  => $iManuelCalidad->id,
            'estado'          => 'emitido',
            'fecha_emision'   => '2026-06-19',
            'reemitido_de_id' => $anulado2->id,
        ]);

        // 20. Fernando (2.° curso) — PENDIENTE
        Certificado::create(['inscripcion_id' => $this->inscribir($fernando, $calidad, '2026-04-02')->id, 'estado' => 'pendiente', 'fecha_emision' => null]);
    }

    private function inscribir(Participante $participante, Curso $curso, string $fechaInscripcion): Inscripcion
    {
        return Inscripcion::create([
            'participante_id'     => $participante->id,
            'curso_id'            => $curso->id,
            'estado_finalizacion' => 'aprobado',
            'fecha_inscripcion'   => $fechaInscripcion,
        ]);
    }
}
