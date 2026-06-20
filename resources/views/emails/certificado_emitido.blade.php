<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 24px;">

    <p style="font-size: 16px;">
        Estimado/a <strong>{{ $certificado->inscripcion->participante->nombre }}</strong>,
    </p>

    <p style="font-size: 15px; line-height: 1.6;">
        Nos complace informarle que su certificado del curso
        <strong>{{ $certificado->inscripcion->curso->nombre }}</strong>
        ha sido emitido exitosamente. Adjunto encontrará el PDF de su certificado.
    </p>

    <table style="background: #f0f4ff; border-left: 4px solid #1e3a8a; border-radius: 4px;
                  padding: 12px 20px; margin: 24px 0; width: auto;">
        <tr>
            <td style="font-size: 11px; color: #6b7280; text-transform: uppercase;
                       letter-spacing: 1px; padding-bottom: 4px;">
                Código de verificación
            </td>
        </tr>
        <tr>
            <td style="font-size: 22px; font-weight: bold; font-family: monospace;
                       color: #1e3a8a; letter-spacing: 4px;">
                {{ $certificado->codigo_verificacion }}
            </td>
        </tr>
    </table>

    <p style="font-size: 14px; color: #555; line-height: 1.6;">
        Docente: {{ $certificado->inscripcion->curso->docente }}<br>
        Fecha de emisión: {{ $certificado->fecha_emision->format('d/m/Y') }}
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

    <p style="font-size: 14px; color: #333;">
        Atentamente,<br>
        <strong>Instituto Habitat</strong>
    </p>

</body>
</html>
