<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Serif', serif;
            background: #ffffff;
            color: #222;
        }

        .pagina {
            width: 100%;
            padding: 10mm;
        }

        .marco-externo {
            border: 6px solid #1e3a8a;
            padding: 5mm;
        }

        .marco-interno {
            border: 2px solid #93c5fd;
            padding: 10mm;
            text-align: center;
        }

        .encabezado-instituto {
            font-size: 9pt;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 1mm;
        }

        .titulo-certificado {
            font-size: 30pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 6px;
            margin-bottom: 5mm;
        }

        .linea-decorativa {
            border-top: 1px solid #bfdbfe;
            width: 60%;
            margin: 0 auto 4mm auto;
        }

        .texto-certifica {
            font-size: 10pt;
            color: #555;
            margin-bottom: 3mm;
        }

        .nombre-participante {
            font-size: 22pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 1mm;
        }

        .dni-participante {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 4mm;
        }

        .texto-aprobacion {
            font-size: 10pt;
            color: #555;
            margin-bottom: 2mm;
        }

        .nombre-curso {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 3mm;
        }

        .detalles-curso {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 6mm;
            line-height: 1.6;
        }

        .pie-certificado {
            border-top: 1px solid #e5e7eb;
            padding-top: 3mm;
            margin-top: 2mm;
        }

        .codigo-label {
            font-size: 7pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .codigo-valor {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 4px;
        }

        .fecha-emision {
            font-size: 8pt;
            color: #9ca3af;
            margin-top: 1mm;
        }
    </style>
</head>
<body>
    <div class="pagina">
        <div class="marco-externo">
            <div class="marco-interno">

                <p class="encabezado-instituto">Instituto Habitat</p>

                <p class="titulo-certificado">CERTIFICADO</p>

                <div class="linea-decorativa"></div>

                <p class="texto-certifica">Certifica que:</p>

                <p class="nombre-participante">{{ $participante->nombre }}</p>
                <p class="dni-participante">DNI: {{ $participante->dni }}</p>

                <p class="texto-aprobacion">
                    ha participado y aprobado satisfactoriamente el curso de especialización:
                </p>

                <p class="nombre-curso">{{ $curso->nombre }}</p>

                <p class="detalles-curso">
                    Dictado por: <strong>{{ $curso->docente }}</strong><br>
                    Período: {{ $curso->fecha_inicio->format('d/m/Y') }} al {{ $curso->fecha_fin->format('d/m/Y') }}
                </p>

                <div class="linea-decorativa"></div>

                <div class="pie-certificado">
                    <p class="codigo-label">Código de verificación</p>
                    <p class="codigo-valor">{{ $certificado->codigo_verificacion }}</p>
                    <p class="fecha-emision">
                        Emitido el {{ $certificado->fecha_emision->format('d/m/Y') }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
