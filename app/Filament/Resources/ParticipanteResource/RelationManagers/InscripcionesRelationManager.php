<?php

namespace App\Filament\Resources\ParticipanteResource\RelationManagers;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Permite inscribir al participante en un curso directo desde su ficha, sin
// pasar por el resource suelto de Inscripciones ni buscarlo de nuevo en un select.
class InscripcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'inscripciones';

    protected static ?string $title = 'Cursos inscritos';

    protected static ?string $modelLabel = 'Inscripción';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('curso_id')
                ->label('Curso')
                ->prefixIcon('heroicon-o-academic-cap')
                ->required()
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    $query = Curso::query();

                    if (filled($search)) {
                        $query->where(function (Builder $q) use ($search): void {
                            $q->where('nombre', 'like', "%{$search}%")
                              ->orWhere('codigo', 'like', "%{$search}%");
                        });
                    }

                    return $query
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn(Curso $curso) => [
                            $curso->id => "{$curso->codigo} - {$curso->nombre}",
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(function (int|string $value): ?string {
                    $curso = Curso::find($value);

                    return $curso ? "{$curso->codigo} - {$curso->nombre}" : null;
                })
                // participante_id no está en el form (lo fija la relación), se toma
                // de getOwnerRecord() igual que en el RelationManager del lado Curso.
                ->rules([
                    fn(Forms\Get $get, ?Inscripcion $record) => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                        $existe = Inscripcion::where('curso_id', $value)
                            ->where('participante_id', $this->getOwnerRecord()->getKey())
                            ->when($record?->id, fn($q, $id) => $q->where('id', '!=', $id))
                            ->exists();
                        if ($existe) {
                            $fail('Este participante ya está inscrito en este curso.');
                        }
                    },
                ])
                ->validationMessages(['required' => 'Debes seleccionar un curso.']),

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
                Tables\Columns\TextColumn::make('curso.codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('curso.nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),

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
                Tables\Actions\CreateAction::make()->label('Inscribir a curso'),
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
            ->emptyStateHeading('Sin cursos inscritos')
            ->emptyStateDescription('Inscribe al participante en un curso con el botón de arriba.');
    }
}
