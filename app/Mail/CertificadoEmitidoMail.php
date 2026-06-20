<?php

namespace App\Mail;

use App\Models\Certificado;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CertificadoEmitidoMail extends Mailable
{
    public function __construct(
        public readonly Certificado $certificado,
        private readonly string $contenidoPdf,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu certificado — ' . $this->certificado->inscripcion->curso->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificado_emitido',
        );
    }

    public function attachments(): array
    {
        $nombreArchivo = 'certificado-' . $this->certificado->codigo_verificacion . '.pdf';

        return [
            Attachment::fromData(fn() => $this->contenidoPdf, $nombreArchivo)
                ->withMime('application/pdf'),
        ];
    }
}
