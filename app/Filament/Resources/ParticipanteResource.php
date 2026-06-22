<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipanteResource\Pages;
use App\Models\Participante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ParticipanteResource extends Resource
{
    protected static ?string $model = Participante::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Participantes';

    protected static ?string $modelLabel = 'Participante';

    protected static ?string $pluralModelLabel = 'Participantes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('dni')
                ->label('DNI')
                ->required()
                ->maxLength(8)
                ->minLength(8)
                ->inputMode('numeric')
                ->extraInputAttributes(['pattern' => '\d*'])
                ->rules(['regex:/^\d{8}$/'])
                ->unique(table: Participante::class, column: 'dni', ignoreRecord: true)
                ->validationMessages([
                    'required'  => 'El DNI es obligatorio.',
                    'min'       => 'El DNI debe tener exactamente 8 dígitos.',
                    'max'       => 'El DNI debe tener exactamente 8 dígitos.',
                    'regex'     => 'El DNI debe contener exactamente 8 dígitos numéricos.',
                    'unique'    => 'Ya existe un participante con ese DNI.',
                ]),

            Forms\Components\TextInput::make('nombre')
                ->label('Nombre completo')
                ->required()
                ->minLength(3)
                ->maxLength(255)
                ->rules(['regex:/^[\p{L}\s.\-\']+$/u'])
                ->extraInputAttributes(['style' => 'text-transform: capitalize'])
                ->helperText('Se guardará con mayúscula inicial en cada palabra.')
                ->validationMessages([
                    'required' => 'El nombre completo es obligatorio.',
                    'min'      => 'El nombre debe tener al menos 3 caracteres.',
                    'regex'    => 'El nombre solo puede contener letras, espacios y guiones.',
                ]),

            Forms\Components\TextInput::make('correo')
                ->label('Correo electrónico')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(table: Participante::class, column: 'correo', ignoreRecord: true)
                ->helperText('Se guardará en minúsculas automáticamente.')
                ->validationMessages([
                    'required' => 'El correo electrónico es obligatorio.',
                    'email'    => 'El correo electrónico no tiene un formato válido.',
                    'unique'   => 'Ya existe un participante con ese correo.',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre completo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inscripciones_count')
                    ->label('Cursos inscritos')
                    ->counts('inscripciones')
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
            ->emptyStateHeading('Sin participantes registrados')
            ->emptyStateDescription('Registra el primer participante con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListParticipantes::route('/'),
            'create' => Pages\CreateParticipante::route('/create'),
            'edit'   => Pages\EditParticipante::route('/{record}/edit'),
        ];
    }
}
