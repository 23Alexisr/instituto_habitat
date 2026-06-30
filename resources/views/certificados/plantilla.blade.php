<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; size: A4 landscape; }

        * { margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #ffffff;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            position: relative;
        }

        .banda-top         { position: absolute; top: 0;    left: 0; right: 0; height: 9mm; background: #8DC63F; }
        .banda-top-oscura  { position: absolute; top: 0;    left: 0; right: 0; height: 3mm; background: #579414; }
        .banda-bottom      { position: absolute; bottom: 0; left: 0; right: 0; height: 5mm; background: #8DC63F; }
        .banda-izq         { position: absolute; top: 9mm; bottom: 5mm; left: 0; width: 4mm; background: #579414; }

        .esquina { position: absolute; width: 13mm; height: 13mm; }
        .esquina-tl { top: 12mm;   left:  7mm; border-top:    2.5pt solid #8DC63F; border-left:  2.5pt solid #8DC63F; }
        .esquina-tr { top: 12mm;   right: 7mm; border-top:    2.5pt solid #8DC63F; border-right: 2.5pt solid #8DC63F; }
        .esquina-bl { bottom: 8mm; left:  7mm; border-bottom: 2.5pt solid #8DC63F; border-left:  2.5pt solid #8DC63F; }
        .esquina-br { bottom: 8mm; right: 7mm; border-bottom: 2.5pt solid #8DC63F; border-right: 2.5pt solid #8DC63F; }

        .contenido {
            position: absolute;
            top: 9mm; bottom: 5mm;
            left: 4mm; right: 0;
            padding: 12mm 14mm 10mm 12mm;
        }

        /* ── Encabezado ── */
        .header-tabla      { width: 100%; border-collapse: collapse; }
        .header-logo       { width: 65mm; vertical-align: middle; }
        .header-logo img   { width: 63mm; height: auto; }
        .header-derecha    { text-align: right; vertical-align: middle; }
        .header-etiqueta   { font-size: 7.5pt; color: #8DC63F; letter-spacing: 3px; text-transform: uppercase; }
        .header-subtitulo  { font-size: 7pt;   color: #b0b0b0; letter-spacing: 1.5px; margin-top: 1mm; }

        /* ── Separadores ── */
        .linea-verde { border: none; border-top: 2pt solid #8DC63F; margin: 7mm 0; }
        .linea-gris  { border: none; border-top: 0.5pt solid #d8efc0; margin: 8mm 30mm; }

        /* ── Cuerpo ── */
        .titulo-cert {
            font-size: 38pt;
            font-weight: bold;
            color: #579414;
            letter-spacing: 8px;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 8mm;
        }

        .texto-otorga {
            font-size: 10pt;
            color: #58595B;
            text-align: center;
            margin-bottom: 8mm;
        }

        .nombre-participante {
            font-family: 'DejaVu Serif', serif;
            font-size: 26pt;
            font-weight: bold;
            color: #2d2d2d;
            text-align: center;
            line-height: 1.15;
            margin-bottom: 4mm;
        }

        .dni-participante {
            font-size: 9pt;
            color: #8DC63F;
            letter-spacing: 3px;
            text-align: center;
            margin-bottom: 12mm;
        }

        .texto-aprobacion {
            font-size: 9.5pt;
            color: #58595B;
            text-align: center;
            margin-bottom: 7mm;
        }

        .nombre-curso {
            font-size: 16pt;
            font-weight: bold;
            color: #579414;
            text-align: center;
            line-height: 1.3;
            margin-bottom: 6mm;
        }

        .detalles-curso {
            font-size: 9pt;
            color: #9a9a9a;
            text-align: center;
            line-height: 2;
            margin-bottom: 12mm;
        }
        .detalles-curso strong { color: #58595B; }

        /* ── Pie ── */
        .pie-tabla  { width: 100%; border-collapse: collapse; }
        .pie-etiqueta {
            font-size: 6.5pt;
            color: #8DC63F;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: block;
            margin-bottom: 1mm;
        }
        .pie-codigo-valor {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 13pt;
            font-weight: bold;
            color: #579414;
            letter-spacing: 5px;
        }
        .pie-fecha-valor { font-size: 9.5pt; color: #58595B; }
        .firma-linea     { border-top: 1pt solid #8DC63F; width: 42mm; margin: 0 auto 2mm auto; }
        .firma-etiqueta  { font-size: 7.5pt; color: #9a9a9a; letter-spacing: 1px; text-align: center; }
    </style>
</head>
<body>
    @php
        $logoBase64 = base64_encode(file_get_contents(public_path('images/logo.svg')));
        $fechaTexto = $certificado->fecha_emision
            ->locale('es')
            ->isoFormat('D [de] MMMM [de] YYYY');
    @endphp

    <div class="banda-top"></div>
    <div class="banda-top-oscura"></div>
    <div class="banda-bottom"></div>
    <div class="banda-izq"></div>

    <div class="esquina esquina-tl"></div>
    <div class="esquina esquina-tr"></div>
    <div class="esquina esquina-bl"></div>
    <div class="esquina esquina-br"></div>

    <div class="contenido">

        {{-- Encabezado --}}
        <table class="header-tabla" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-logo">
                    <img src="data:image/svg+xml;base64,{{ $logoBase64 }}" alt="Logo IPEH">
                </td>
                <td class="header-derecha">
                    <span class="header-etiqueta">Certificado de Participación</span>
                    <div class="header-subtitulo">Sistema Nacional de Certificaciones</div>
                </td>
            </tr>
        </table>

        <hr class="linea-verde">

        {{-- Título --}}
        <p class="titulo-cert">Certificado</p>

        <p class="texto-otorga">otorga el presente certificado a:</p>

        {{-- Participante --}}
        <p class="nombre-participante">{{ $participante->nombre }}</p>
        <p class="dni-participante">D.N.I. &nbsp; {{ $participante->dni }}</p>

        <hr class="linea-gris">

        {{-- Curso --}}
        <p class="texto-aprobacion">
            por haber participado y aprobado satisfactoriamente el curso de especialización:
        </p>

        <p class="nombre-curso">{{ $curso->nombre }}</p>

        <p class="detalles-curso">
            Docente: <strong>{{ $curso->docente }}</strong><br>
            Período: {{ $curso->fecha_inicio->format('d/m/Y') }} al {{ $curso->fecha_fin->format('d/m/Y') }}
        </p>

        <hr class="linea-verde">

        {{-- Pie --}}
        <table class="pie-tabla" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%; vertical-align: middle;">
                    <span class="pie-etiqueta">Código de verificación</span>
                    <span class="pie-codigo-valor">{{ $certificado->codigo_verificacion }}</span>
                </td>
                <td style="width: 26%; vertical-align: bottom; padding-bottom: 2mm;">
                    <div class="firma-linea"></div>
                    <p class="firma-etiqueta">Director Académico</p>
                </td>
                <td style="width: 24%; text-align: right; vertical-align: middle;">
                    <span class="pie-etiqueta">Fecha de emisión</span>
                    <span class="pie-fecha-valor" style="display:block;">{{ $fechaTexto }}</span>
                </td>
            </tr>
        </table>

        {{-- QR flotante a la derecha, a la altura del curso --}}
        <img src="data:image/png;base64,{{ $qrBase64 }}"
             style="position: absolute; right: 14mm; bottom: 28mm; width: 22mm; height: 22mm;">

    </div>
</body>
</html>