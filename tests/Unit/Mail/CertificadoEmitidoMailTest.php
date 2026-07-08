<?php

namespace Tests\Unit\Mail;

use App\Mail\CertificadoEmitidoMail;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificadoEmitidoMailTest extends TestCase
{
    use RefreshDatabase;

    private function crearCertificado(): Certificado
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
            'estado' => 'emitido',
            'fecha_emision' => '2026-02-01',
        ]);
    }

    // envelope() arma el subject concatenando el nombre del curso via inscripcion->curso
    public function test_envelope_incluye_nombre_del_curso_en_el_asunto(): void
    {
        $certificado = $this->crearCertificado();

        $mail = new CertificadoEmitidoMail($certificado, 'contenido-pdf-falso');

        $this->assertSame('Tu certificado - Curso De Prueba', $mail->envelope()->subject);
    }

    // attachments() arma Attachment::fromData con el contenido PDF pasado al constructor, no lee el disco
    public function test_incluye_adjunto_pdf_con_nombre_y_mime_correcto(): void
    {
        $certificado = $this->crearCertificado();

        $mail = new CertificadoEmitidoMail($certificado, 'contenido-pdf-falso');

        $adjuntos = $mail->attachments();

        $this->assertCount(1, $adjuntos);
        $this->assertSame('application/pdf', $adjuntos[0]->mime);
        $this->assertSame('certificado-' . $certificado->codigo_verificacion . '.pdf', $adjuntos[0]->as);
    }

    // $certificado es propiedad publica del Mailable, Laravel la expone sola a la vista sin pasar "with"
    public function test_vista_renderiza_datos_del_participante_y_curso(): void
    {
        $certificado = $this->crearCertificado();

        $mail = new CertificadoEmitidoMail($certificado, 'contenido-pdf-falso');

        $mail->assertSeeInHtml('Pedro Sanchez');
        $mail->assertSeeInHtml('Curso De Prueba');
        $mail->assertSeeInHtml('Juan Perez');
    }
}
