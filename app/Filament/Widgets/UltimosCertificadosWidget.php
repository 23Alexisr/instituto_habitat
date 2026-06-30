<?php

namespace App\Filament\Widgets;

use App\Models\Certificado;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosCertificadosWidget extends BaseWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Actividad reciente — Certificados';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Certificado::query()
                    ->with(['inscripcion.participante', 'inscripcion.curso'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('inscripcion.participante.nombre')
                    ->label('Participante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inscripcion.participante.dni')
                    ->label('DNI')
                    ->searchable(),

                Tables\Columns\TextColumn::make('inscripcion.curso.nombre')
                    ->label('Curso')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('codigo_verificacion')
                    ->label('Código')
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('Código copiado'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'emitido'  => 'success',
                        'pendiente' => 'warning',
                        'anulado'  => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Fecha emisión')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->paginated(false)
            ->striped();
    }
}
