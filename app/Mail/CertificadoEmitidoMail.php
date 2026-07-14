<?php

namespace App\Mail;

use App\Models\Certificado;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CertificadoEmitidoMail extends Mailable
{
    // recibo el certificado completo (con su relación a inscripción ya disponible)
    // y el PDF ya generado como string, para no tener que generarlo de nuevo acá dentro
    public function __construct(
        public readonly Certificado $certificado,
        private readonly string $contenidoPdf,
    ) {}

    // el asunto del correo arma el nombre del curso navegando la relación
    // certificado -> inscripcion -> curso -> nombre
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu certificado - ' . $this->certificado->inscripcion->curso->nombre,
        );
    }

    // el cuerpo del correo es una vista blade aparte,
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificado_emitido',
        );
    }

    // adjunto el PDF que ya me llegó como string (no lo genero acá, solo lo empaqueto)
    // uso fromData con closure porque el PDF no viene de un archivo en disco, viene de memoria
    public function attachments(): array
    {
        $nombreArchivo = 'certificado-' . $this->certificado->codigo_verificacion . '.pdf';

        return [
            Attachment::fromData(fn() => $this->contenidoPdf, $nombreArchivo)
                ->withMime('application/pdf'),
        ];
    }
}