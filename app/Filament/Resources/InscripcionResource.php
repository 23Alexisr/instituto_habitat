<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscripcionResource\Pages;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InscripcionResource extends Resource
{
    protected static ?string $model = Inscripcion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Inscripciones';

    protected static ?string $modelLabel = 'Inscripción';

    protected static ?string $pluralModelLabel = 'Inscripciones';

    protected static ?string $slug = 'inscripciones';

    protected static ?string $navigationGroup = 'Academia';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la inscripción')
                ->description('Vincula un participante con un curso y registra su estado de finalización.')
                ->icon('heroicon-o-clipboard-document-list')
                ->columns(2)
                ->schema([
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
                        ->validationMessages(['required' => 'Debes seleccionar un participante.']),

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
                        // Regla manual (no ->unique de Filament) porque la unicidad es compuesta:
                        // el mismo curso puede repetirse con otro participante y viceversa.
                        ->rules([
                            fn(Forms\Get $get, ?Inscripcion $record) => function (string $attribute, mixed $value, \Closure $fail) use ($get, $record): void {
                                $participanteId = $get('participante_id');
                                if (! $participanteId || ! $value) {
                                    return;
                                }
                                $existe = Inscripcion::where('participante_id', $participanteId)
                                    ->where('curso_id', $value)
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
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('participante.nombre')
                    ->label('Participante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('participante.dni')
                    ->label('DNI')
                    ->searchable(),

                Tables\Columns\TextColumn::make('curso.nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('curso.codigo')
                    ->label('Código')
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

                // Columna virtual (no existe en la tabla): resume de un vistazo si ya
                // hay certificado para esta inscripción, sin tener que ir al otro resource.
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
            ->filters([
                Tables\Filters\SelectFilter::make('curso_id')
                    ->label('Curso')
                    ->relationship('curso', 'nombre')
                    ->searchable()
                    ->preload(),

                // "Pendiente" no es un valor guardado en la columna, es NULL. El query
                // custom traduce esa opción del filtro a whereNull en vez de comparar valor.
                Tables\Filters\SelectFilter::make('estado_finalizacion')
                    ->label('Estado')
                    ->options([
                        'aprobado'    => 'Aprobado',
                        'desaprobado' => 'Desaprobado',
                        'pendiente'   => 'Pendiente',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['value'] ?? null),
                            fn(Builder $q) => $data['value'] === 'pendiente'
                                ? $q->whereNull('estado_finalizacion')
                                : $q->where('estado_finalizacion', $data['value']),
                        );
                    }),

                Tables\Filters\Filter::make('dni_participante')
                    ->label('DNI del participante')
                    ->form([
                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->maxLength(8)
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                'pattern'  => '\d*',
                            ]),
                    ])
                    ->query(fn(Builder $query, array $data): Builder =>
                        $query->when(
                            filled($data['dni'] ?? null),
                            fn($q) => $q->whereHas(
                                'participante',
                                fn($q) => $q->where('dni', 'like', '%' . $data['dni'] . '%')
                            )
                        )
                    )
                    ->indicateUsing(fn(array $data): ?string =>
                        filled($data['dni'] ?? null) ? 'DNI: ' . $data['dni'] : null
                    ),
            ])
            ->actions([
                // Atajo pa no ir al resource de Certificados y buscar la inscripción de nuevo:
                // solo aparece si aprobó el curso y todavía no tiene un certificado vigente.
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

                // La FK certificados.inscripcion_id tiene cascadeOnDelete a nivel de BD:
                // sin este bloqueo, borrar la inscripción borra en silencio sus certificados
                // (incluyendo emitidos), perdiendo el historial. Halt cancela la acción.
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
                // Para cerrar un curso completo (ej. 30 alumnos) sin editar fila por fila.
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('marcar_aprobado')
                        ->label('Marcar como aprobado')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(
                            function (Collection $records): void {
                                $records->each(fn(Model $record) => $record->update(['estado_finalizacion' => 'aprobado']));

                                Notification::make()
                                    ->title('Inscripciones actualizadas')
                                    ->body('Se marcaron como aprobadas.')
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('marcar_desaprobado')
                        ->label('Marcar como desaprobado')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(
                            function (Collection $records): void {
                                $records->each(fn(Model $record) => $record->update(['estado_finalizacion' => 'desaprobado']));

                                Notification::make()
                                    ->title('Inscripciones actualizadas')
                                    ->body('Se marcaron como desaprobadas.')
                                    ->success()
                                    ->send();
                            }
                        )
                        ->deselectRecordsAfterCompletion(),

                    // Misma protección que el DeleteAction individual, aplicada al lote.
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->before(
                            function (Collection $records): void {
                                if ($records->contains(fn(Model $record) => $record instanceof Inscripcion && $record->certificados()->exists())) {
                                    Notification::make()
                                        ->title('No se puede eliminar')
                                        ->body('Al menos una inscripción seleccionada tiene certificados asociados. Elimínalos primero desde el módulo de Certificados.')
                                        ->danger()
                                        ->send();

                                    throw new Halt();
                                }
                            }
                        ),
                ]),
            ])
            ->emptyStateHeading('Sin inscripciones registradas')
            ->emptyStateDescription('Crea la primera inscripción con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInscripciones::route('/'),
            'create' => Pages\CreateInscripcion::route('/create'),
            'edit'   => Pages\EditInscripcion::route('/{record}/edit'),
        ];
    }
}
