<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Cursos';

    protected static ?string $modelLabel = 'Curso';

    protected static ?string $pluralModelLabel = 'Cursos';

    protected static ?string $navigationGroup = 'Academia';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label('Nombre del curso')
                ->required()
                ->minLength(3)
                ->maxLength(255)
                ->extraInputAttributes(['style' => 'text-transform: capitalize'])
                ->helperText('Se guardará con mayúscula inicial en cada palabra.')
                ->live(debounce: 500)
                ->afterStateUpdated(function (?string $state, Forms\Set $set, string $operation) {
                    if ($operation !== 'create' || blank($state)) return;
                    $set('codigo', self::generarCodigo($state));
                })
                ->validationMessages([
                    'required' => 'El nombre del curso es obligatorio.',
                    'min'      => 'El nombre del curso debe tener al menos 3 caracteres.',
                ]),

            Forms\Components\TextInput::make('codigo')
                ->label('Código')
                ->required()
                ->readOnly()
                ->extraInputAttributes(['style' => 'text-transform: uppercase; background:#f9fafb; cursor:not-allowed'])
                ->helperText('Generado automáticamente a partir del nombre del curso.')
                ->unique(table: Curso::class, column: 'codigo', ignoreRecord: true)
                ->validationMessages([
                    'required' => 'El código es obligatorio.',
                    'unique'   => 'Ya existe un curso con ese código.',
                ]),

            Forms\Components\TextInput::make('docente')
                ->label('Docente')
                ->required()
                ->minLength(3)
                ->maxLength(255)
                ->rules(['regex:/^[\p{L}\s.\-\']+$/u'])
                ->extraInputAttributes(['style' => 'text-transform: capitalize'])
                ->helperText('Se guardará con mayúscula inicial en cada palabra.')
                ->validationMessages([
                    'required' => 'El nombre del docente es obligatorio.',
                    'min'      => 'El nombre del docente debe tener al menos 3 caracteres.',
                    'regex'    => 'El nombre del docente solo puede contener letras, espacios y guiones.',
                ]),

            Forms\Components\DatePicker::make('fecha_inicio')
                ->label('Fecha de inicio')
                ->required()
                ->displayFormat('d/m/Y')
                ->validationMessages([
                    'required' => 'La fecha de inicio es obligatoria.',
                ]),

            Forms\Components\DatePicker::make('fecha_fin')
                ->label('Fecha de fin')
                ->required()
                ->displayFormat('d/m/Y')
                ->afterOrEqual('fecha_inicio')
                ->validationMessages([
                    'required'       => 'La fecha de fin es obligatoria.',
                    'after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('docente')
                    ->label('Docente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
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
            ->emptyStateHeading('Sin cursos registrados')
            ->emptyStateDescription('Crea el primer curso con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit'   => Pages\EditCurso::route('/{record}/edit'),
        ];
    }

    public static function generarCodigo(string $nombre): string
    {
        $omitir = ['de', 'del', 'la', 'el', 'las', 'los', 'un', 'una', 'y', 'e', 'o', 'en', 'a', 'con', 'por', 'para', 'al'];

        $palabras = preg_split('/\s+/', trim($nombre));

        $iniciales = '';
        foreach ($palabras as $palabra) {
            $limpia = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', '', $palabra);
            if ($limpia === '' || in_array(mb_strtolower($limpia), $omitir)) continue;
            $iniciales .= mb_strtoupper(mb_substr($limpia, 0, 1));
        }

        if ($iniciales === '') {
            $iniciales = mb_strtoupper(mb_substr($nombre, 0, 3));
        }

        return $iniciales . '-' . date('Y');
    }
}
