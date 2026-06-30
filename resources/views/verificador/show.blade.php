<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Certificado — Instituto Habitat</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f7e6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: #2d2d2d;
        }

        .card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(87, 148, 20, 0.15);
            width: 100%;
            max-width: 560px;
            overflow: hidden;
        }

        /* Barra verde superior */
        .card-header {
            background: #8DC63F;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .card-header img { height: 42px; }
        .card-header-text {
            color: #fff;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .card-body { padding: 2rem; }

        /* Estado: VÁLIDO */
        .estado-valido {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #f0f9e6;
            border: 1.5px solid #8DC63F;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        .estado-valido .icono { font-size: 1.75rem; }
        .estado-valido .titulo { font-size: 1rem; font-weight: 700; color: #579414; }
        .estado-valido .subtitulo { font-size: 0.8rem; color: #6b7280; margin-top: 0.15rem; }

        /* Estado: ANULADO */
        .estado-anulado {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fff5f5;
            border: 1.5px solid #ef4444;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        .estado-anulado .icono { font-size: 1.75rem; }
        .estado-anulado .titulo { font-size: 1rem; font-weight: 700; color: #dc2626; }
        .estado-anulado .subtitulo { font-size: 0.8rem; color: #6b7280; margin-top: 0.15rem; }

        /* Estado: NO ENCONTRADO */
        .estado-noenc {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fafafa;
            border: 1.5px solid #d1d5db;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        .estado-noenc .icono { font-size: 1.75rem; }
        .estado-noenc .titulo { font-size: 1rem; font-weight: 700; color: #374151; }
        .estado-noenc .subtitulo { font-size: 0.8rem; color: #6b7280; margin-top: 0.15rem; }

        /* Detalles */
        .detalle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .detalle-item label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8DC63F;
            font-weight: 600;
            display: block;
            margin-bottom: 0.2rem;
        }
        .detalle-item span {
            font-size: 0.92rem;
            color: #2d2d2d;
            font-weight: 500;
        }
        .detalle-full { grid-column: span 2; }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 1.25rem 0;
        }

        .codigo-tag {
            display: inline-block;
            font-family: monospace;
            font-size: 1rem;
            font-weight: 700;
            color: #579414;
            letter-spacing: 0.25em;
            background: #f0f7e6;
            padding: 0.3rem 0.75rem;
            border-radius: 0.4rem;
        }

        .motivo-box {
            background: #fff5f5;
            border-left: 3px solid #ef4444;
            padding: 0.75rem 1rem;
            border-radius: 0 0.5rem 0.5rem 0;
            font-size: 0.88rem;
            color: #374151;
            margin-top: 0.75rem;
        }

        .footer-nota {
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="card-header">
            <img src="{{ asset('images/logo.svg') }}" alt="Instituto Habitat">
            <span class="card-header-text">Verificación de certificado</span>
        </div>

        <div class="card-body">

            @if (!$certificado)
                {{-- No encontrado --}}
                <div class="estado-noenc">
                    <span class="icono">🔍</span>
                    <div>
                        <div class="titulo">Certificado no encontrado</div>
                        <div class="subtitulo">El código <strong>{{ strtoupper($codigo) }}</strong> no existe en el sistema.</div>
                    </div>
                </div>

            @elseif ($certificado->estado === 'anulado')
                {{-- Anulado --}}
                <div class="estado-anulado">
                    <span class="icono">❌</span>
                    <div>
                        <div class="titulo">Certificado anulado</div>
                        <div class="subtitulo">Este certificado fue dado de baja y no es válido.</div>
                    </div>
                </div>

                <div class="detalle-grid">
                    <div class="detalle-item detalle-full">
                        <label>Participante</label>
                        <span>{{ $certificado->inscripcion->participante->nombre }}</span>
                    </div>
                    <div class="detalle-item">
                        <label>DNI</label>
                        <span>{{ $certificado->inscripcion->participante->dni }}</span>
                    </div>
                    <div class="detalle-item">
                        <label>Código</label>
                        <span class="codigo-tag">{{ $certificado->codigo_verificacion }}</span>
                    </div>
                </div>

                @if ($certificado->motivo_anulacion)
                    <div class="motivo-box">
                        <strong>Motivo:</strong> {{ $certificado->motivo_anulacion }}
                    </div>
                @endif

            @else
                {{-- Válido (emitido o pendiente) --}}
                <div class="estado-valido">
                    <span class="icono">✅</span>
                    <div>
                        <div class="titulo">Certificado válido</div>
                        <div class="subtitulo">Emitido por el Instituto Peruano de Estudios Habitat.</div>
                    </div>
                </div>

                <div class="detalle-grid">
                    <div class="detalle-item detalle-full">
                        <label>Participante</label>
                        <span>{{ $certificado->inscripcion->participante->nombre }}</span>
                    </div>
                    <div class="detalle-item">
                        <label>DNI</label>
                        <span>{{ $certificado->inscripcion->participante->dni }}</span>
                    </div>
                    <div class="detalle-item">
                        <label>Código</label>
                        <span class="codigo-tag">{{ $certificado->codigo_verificacion }}</span>
                    </div>

                    <hr class="divider" style="grid-column:span 2">

                    <div class="detalle-item detalle-full">
                        <label>Curso</label>
                        <span>{{ $certificado->inscripcion->curso->nombre }}</span>
                    </div>
                    <div class="detalle-item">
                        <label>Docente</label>
                        <span>{{ $certificado->inscripcion->curso->docente }}</span>
                    </div>
                    <div class="detalle-item">
                        <label>Período</label>
                        <span>
                            {{ $certificado->inscripcion->curso->fecha_inicio->format('d/m/Y') }}
                            al
                            {{ $certificado->inscripcion->curso->fecha_fin->format('d/m/Y') }}
                        </span>
                    </div>

                    @if ($certificado->fecha_emision)
                        <div class="detalle-item">
                            <label>Fecha de emisión</label>
                            <span>{{ $certificado->fecha_emision->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <p class="footer-nota">
        Instituto Peruano de Estudios Habitat &mdash; Sistema de verificación de certificados
    </p>

</body>
</html>
