<?php

namespace App\Filament\Resources\CursoResource\RelationManagers;

use App\Models\Certificado;
use App\Models\Inscripcion;
use App\Models\Participante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Permite inscribir participantes directo desde la ficha del curso, sin pasar
// por el resource suelto de Inscripciones ni buscar el curso de nuevo en un select.
class InscripcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'inscripciones';

    protected static ?string $title = 'Participantes inscritos';

    protected static ?string $modelLabel = 'Inscripción';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('participante_id')
                ->label('Participante')
                ->prefixIcon('heroicon-o-user')
                ->required()
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    $query = Participante::query();

                    if (filled($search)) {
                        $query->where(function (Builder $q) use ($search): void {
                            $q->where('nombre', 'like', "%{$search}%")
                              ->orWhere('dni', 'like', "%{$search}%");
                        });
                    }

                    return $query
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn(Participante $participante) => [
                            $participante->id => "{$participante->nombre} - DNI: {$participante->dni}",
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(function (int|string $value): ?string {
                    $participante = Participante::find($value);

                    return $participante
                        ? "{$participante->nombre} - DNI: {$participante->dni}"
                        : null;
                })
                // curso_id no está en el form (ya lo fija el RelationManager vía la relación),
                // así que se toma de getOwnerRecord() en vez de un Forms\Get('curso_id').
                ->rules([
                    fn(Forms\Get $get, ?Inscripcion $record) => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                        $existe = Inscripcion::where('participante_id', $value)
                            ->where('curso_id', $this->getOwnerRecord()->id)
                            ->when($record?->id, fn($q, $id) => $q->where('id', '!=', $id))
                            ->exists();
                        if ($existe) {
                            $fail('Este participante ya está inscrito en este curso.');
                        }
                    },
                ])
                ->validationMessages(['required' => 'Debes seleccionar un participante.']),

            Forms\Components\DatePicker::make('fecha_inscripcion')
                ->label('Fecha de inscripción')
                ->prefixIcon('heroicon-o-calendar')
                ->required()
                ->default(today())
                ->displayFormat('d/m/Y')
                ->validationMessages(['required' => 'La fecha de inscripción es obligatoria.']),

            Forms\Components\Select::make('estado_finalizacion')
                ->label('Estado de finalización')
                ->prefixIcon('heroicon-o-flag')
                ->options([
                    'aprobado'    => 'Aprobado',
                    'desaprobado' => 'Desaprobado',
                ])
                ->placeholder('Pendiente de evaluación')
                ->helperText('Déjalo en blanco si el curso aún no ha finalizado.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('participante.nombre')
                    ->label('Participante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('participante.dni')
                    ->label('DNI')
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado_finalizacion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'aprobado'    => 'success',
                        'desaprobado' => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'aprobado'    => 'Aprobado',
                        'desaprobado' => 'Desaprobado',
                        default       => 'Pendiente',
                    }),

                Tables\Columns\TextColumn::make('fecha_inscripcion')
                    ->label('Fecha inscripción')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('certificado_estado')
                    ->label('Certificado')
                    ->badge()
                    ->getStateUsing(fn(Inscripcion $record): string => match (true) {
                        $record->certificadoVigente?->estaEmitido() => 'Emitido',
                        $record->certificadoVigente?->estaPendiente() => 'Por generar',
                        default => 'Sin certificado',
                    })
                    ->color(fn(Inscripcion $record): string => match (true) {
                        $record->certificadoVigente?->estaEmitido() => 'success',
                        $record->certificadoVigente?->estaPendiente() => 'warning',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Inscribir participante'),
            ])
            ->actions([
                Tables\Actions\Action::make('generar_certificado')
                    ->label('Generar certificado')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(fn(Inscripcion $record): bool =>
                        $record->estado_finalizacion === 'aprobado' && ! $record->certificadoVigente
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Generar certificado')
                    ->modalDescription('Se creará un certificado en estado pendiente para esta inscripción. Podrás emitirlo desde el módulo de Certificados.')
                    ->modalSubmitActionLabel('Generar')
                    ->action(function (Inscripcion $record): void {
                        Certificado::create([
                            'inscripcion_id' => $record->id,
                            'estado'         => 'pendiente',
                        ]);

                        Notification::make()
                            ->title('Certificado generado')
                            ->body('Certificado pendiente creado. Emítelo desde el módulo de Certificados.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('Editar'),

                // Misma protección que InscripcionResource: certificados.inscripcion_id
                // cascadea en la BD, así que si tiene certificados no se deja borrar.
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->before(function (Inscripcion $record): void {
                        if ($record->certificados()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar')
                                ->body('Esta inscripción tiene certificados asociados. Elimínalos primero desde el módulo de Certificados.')
                                ->danger()
                                ->send();

                            throw new Halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('Sin participantes inscritos')
            ->emptyStateDescription('Inscribe al primer participante con el botón de arriba.');
    }
}
