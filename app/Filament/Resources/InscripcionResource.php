<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscripcionResource\Pages;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Participante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InscripcionResource extends Resource
{
    protected static ?string $model = Inscripcion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Inscripciones';

    protected static ?string $modelLabel = 'Inscripción';

    protected static ?string $pluralModelLabel = 'Inscripciones';

    protected static ?string $slug = 'inscripciones';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('participante_id')
                ->label('Participante')
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
                            $participante->id => "{$participante->nombre} — DNI: {$participante->dni}",
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(function (int|string $value): ?string {
                    $participante = Participante::find($value);

                    return $participante
                        ? "{$participante->nombre} — DNI: {$participante->dni}"
                        : null;
                })
                ->validationMessages(['required' => 'Debes seleccionar un participante.']),

            Forms\Components\Select::make('curso_id')
                ->label('Curso')
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
                            $curso->id => "{$curso->codigo} — {$curso->nombre}",
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(function (int|string $value): ?string {
                    $curso = Curso::find($value);

                    return $curso ? "{$curso->codigo} — {$curso->nombre}" : null;
                })
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
                ->required()
                ->default(today())
                ->displayFormat('d/m/Y')
                ->validationMessages(['required' => 'La fecha de inscripción es obligatoria.']),

            Forms\Components\Select::make('estado_finalizacion')
                ->label('Estado de finalización')
                ->options([
                    'aprobado'    => 'Aprobado',
                    'desaprobado' => 'Desaprobado',
                ])
                ->placeholder('Pendiente de evaluación')
                ->helperText('Déjalo en blanco si el curso aún no ha finalizado.'),
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
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
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
