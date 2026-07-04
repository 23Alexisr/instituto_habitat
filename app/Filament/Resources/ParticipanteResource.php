<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipanteResource\Pages;
use App\Filament\Resources\ParticipanteResource\RelationManagers;
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

    protected static ?string $navigationGroup = 'Academia';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\Section::make('Foto')
                        ->icon('heroicon-o-camera')
                        ->schema([
                            Forms\Components\FileUpload::make('foto')
                                ->label(false)
                                ->avatar()
                                ->image()
                                ->imageEditor()
                                ->circleCropper()
                                ->disk('participantes')
                                ->maxSize(2048),
                        ])
                        ->columnSpan(1),

                    Forms\Components\Section::make('Datos del participante')
                        ->description('Complete la información básica para registrar al participante.')
                        ->icon('heroicon-o-user-plus')
                        ->columns(2)
                        ->columnSpan(2)
                        ->schema([
                            Forms\Components\TextInput::make('dni')
                                ->label('DNI')
                                ->autofocus()
                                ->prefixIcon('heroicon-o-identification')
                                ->placeholder('12345678')
                                ->required()
                                ->maxLength(8)
                                ->minLength(8)
                                ->inputMode('numeric')
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                    'pattern'  => '\d*',
                                ])
                                ->rules(['regex:/^\d{8}$/'])
                                ->unique(table: Participante::class, column: 'dni', ignoreRecord: true)
                                ->validationMessages([
                                    'required'  => 'El DNI es obligatorio.',
                                    'min'       => 'El DNI debe tener exactamente 8 dígitos.',
                                    'max'       => 'El DNI debe tener exactamente 8 dígitos.',
                                    'regex'     => 'El DNI debe contener exactamente 8 dígitos numéricos.',
                                    'unique'    => 'Ya existe un participante con ese DNI.',
                                ]),

                            Forms\Components\TextInput::make('telefono')
                                ->label('Teléfono')
                                ->prefixIcon('heroicon-o-phone')
                                ->placeholder('987654321')
                                ->tel()
                                ->inputMode('numeric')
                                ->maxLength(20)
                                ->extraInputAttributes([
                                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                ]),

                            Forms\Components\TextInput::make('correo')
                                ->label('Correo electrónico')
                                ->prefixIcon('heroicon-o-envelope')
                                ->placeholder('nombre@dominio.com')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->rules(['email:rfc'])
                                ->unique(table: Participante::class, column: 'correo', ignoreRecord: true)
                                ->validationMessages([
                                    'required' => 'El correo electrónico es obligatorio.',
                                    'email'    => 'El correo no es válido. Debe incluir @ y un dominio (ej: usuario@gmail.com).',
                                    'unique'   => 'Ya existe un participante con ese correo.',
                                ]),

                            Forms\Components\Select::make('genero')
                                ->label('Género')
                                ->prefixIcon('heroicon-o-users')
                                ->placeholder('Seleccione')
                                ->options([
                                    'masculino' => 'Masculino',
                                    'femenino'  => 'Femenino',
                                    'otro'      => 'Otro',
                                ]),

                            Forms\Components\DatePicker::make('fecha_nacimiento')
                                ->label('Fecha de nacimiento')
                                ->prefixIcon('heroicon-o-cake')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now()),

                            Forms\Components\TextInput::make('nombre')
                                ->label('Nombre completo')
                                ->prefixIcon('heroicon-o-user')
                                ->placeholder('Ej: Juan Pérez García')
                                ->columnSpanFull()
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

                            Forms\Components\TextInput::make('direccion')
                                ->label('Dirección')
                                ->prefixIcon('heroicon-o-map-pin')
                                ->placeholder('Ej: Av. Los Álamos 123, Lima')
                                ->columnSpanFull()
                                ->maxLength(255),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->disk('participantes')
                    ->circular(),

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

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('genero')
                    ->label('Género')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'masculino' => 'Masculino',
                        'femenino'  => 'Femenino',
                        'otro'      => 'Otro',
                        default     => '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

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

    public static function getRelations(): array
    {
        return [
            RelationManagers\InscripcionesRelationManager::class,
        ];
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
