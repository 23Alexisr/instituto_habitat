<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificadoResource\Pages;
use App\Mail\CertificadoEmitidoMail;
use App\Models\Certificado;
use App\Models\Inscripcion;
use App\Services\ServicioCertificadoPdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class CertificadoResource extends Resource
{
    protected static ?string $model = Certificado::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Certificados';

    protected static ?string $modelLabel = 'Certificado';

    protected static ?string $pluralModelLabel = 'Certificados';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('inscripcion_id')
                ->label('Participante / Curso')
                ->required()
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    $query = Inscripcion::with(['participante', 'curso'])
                        ->where('estado_finalizacion', 'aprobado');

                    if (filled($search)) {
                        $query->where(function (Builder $q) use ($search): void {
                            $q->whereHas('participante', fn(Builder $q) =>
                                $q->where('nombre', 'like', "%{$search}%")
                                  ->orWhere('dni', 'like', "%{$search}%")
                            )
                            ->orWhereHas('curso', fn(Builder $q) =>
                                $q->where('nombre', 'like', "%{$search}%")
                            );
                        });
                    }

                    return $query
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn(Inscripcion $inscripcion) => [
                            $inscripcion->id => "{$inscripcion->participante->nombre} — {$inscripcion->curso->nombre}",
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(function (int|string $value): ?string {
                    $inscripcion = Inscripcion::with(['participante', 'curso'])->find($value);

                    return $inscripcion
                        ? "{$inscripcion->participante->nombre} — {$inscripcion->curso->nombre}"
                        : null;
                })
                ->rules([
                    function () {
                        return function (string $attribute, mixed $value, \Closure $fail): void {
                            $inscripcion = Inscripcion::find($value);
                            if (! $inscripcion || $inscripcion->estado_finalizacion !== 'aprobado') {
                                $fail('El participante debe tener estado aprobado en el curso para emitir un certificado.');
                            }
                        };
                    },
                ])
                ->validationMessages([
                    'required' => 'Debes seleccionar una inscripción aprobada.',
                ])
                ->helperText('Busca por nombre del participante, DNI o nombre del curso.'),

            Forms\Components\Select::make('estado')
                ->label('Estado')
                ->required()
                ->options([
                    'pendiente' => 'Pendiente',
                    'emitido'   => 'Emitido',
                    'anulado'   => 'Anulado',
                ])
                ->default('pendiente')
                ->live()
                ->validationMessages([
                    'required' => 'El estado del certificado es obligatorio.',
                ]),

            Forms\Components\DatePicker::make('fecha_emision')
                ->label('Fecha de emisión')
                ->displayFormat('d/m/Y')
                ->visible(fn(Forms\Get $get): bool => $get('estado') === 'emitido')
                ->required(fn(Forms\Get $get): bool => $get('estado') === 'emitido'),

            Forms\Components\Textarea::make('motivo_anulacion')
                ->label('Motivo de anulación')
                ->rows(3)
                ->minLength(10)
                ->maxLength(1000)
                ->visible(fn(Forms\Get $get): bool => $get('estado') === 'anulado')
                ->required(fn(Forms\Get $get): bool => $get('estado') === 'anulado')
                ->helperText('Describe el error con suficiente detalle para el historial.')
                ->validationMessages([
                    'required' => 'Debes ingresar el motivo de anulación.',
                    'min'      => 'El motivo debe tener al menos 10 caracteres.',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inscripcion.participante.nombre')
                    ->label('Participante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inscripcion.participante.dni')
                    ->label('DNI')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('inscripcion.curso.codigo')
                    ->label('Cód. curso')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('inscripcion.curso.nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('codigo_verificacion')
                    ->label('Código')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'emitido' => 'success',
                        'anulado' => 'danger',
                        default   => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'pendiente' => 'Pendiente',
                        'emitido'   => 'Emitido',
                        'anulado'   => 'Anulado',
                        default     => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Emisión')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('certificadoOriginal.codigo_verificacion')
                    ->label('Reemite a')
                    ->fontFamily('mono')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'emitido'   => 'Emitido',
                        'anulado'   => 'Anulado',
                    ]),

                Tables\Filters\Filter::make('dni_participante')
                    ->label('DNI del participante')
                    ->form([
                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->maxLength(8)
                            ->inputMode('numeric')
                            ->extraInputAttributes(['pattern' => '\d*']),
                    ])
                    ->query(fn(Builder $query, array $data): Builder =>
                        $query->when(
                            filled($data['dni'] ?? null),
                            fn($q) => $q->whereHas(
                                'inscripcion.participante',
                                fn($q) => $q->where('dni', 'like', '%' . $data['dni'] . '%')
                            )
                        )
                    )
                    ->indicateUsing(fn(array $data): ?string =>
                        filled($data['dni'] ?? null) ? 'DNI: ' . $data['dni'] : null
                    ),

                Tables\Filters\Filter::make('codigo_curso')
                    ->label('Código de curso')
                    ->form([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código del curso')
                            ->maxLength(20),
                    ])
                    ->query(fn(Builder $query, array $data): Builder =>
                        $query->when(
                            filled($data['codigo'] ?? null),
                            fn($q) => $q->whereHas(
                                'inscripcion.curso',
                                fn($q) => $q->where('codigo', 'like', '%' . $data['codigo'] . '%')
                            )
                        )
                    )
                    ->indicateUsing(fn(array $data): ?string =>
                        filled($data['codigo'] ?? null) ? 'Curso: ' . $data['codigo'] : null
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('emitir')
                    ->label('Emitir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Certificado $record): bool => $record->estaPendiente())
                    ->requiresConfirmation()
                    ->modalHeading('Emitir certificado')
                    ->modalDescription('Se generará el código de verificación, se actualizará el estado y se enviará el PDF al correo del participante.')
                    ->modalSubmitActionLabel('Confirmar emisión')
                    ->action(function (Certificado $record): void {
                        if (empty($record->codigo_verificacion)) {
                            $record->codigo_verificacion = Certificado::generarCodigoVerificacion();
                        }
                        $record->estado = 'emitido';
                        $record->fecha_emision = now()->toDateString();
                        $record->save();

                        $record->load('inscripcion.participante', 'inscripcion.curso');
                        $correo = $record->inscripcion->participante->correo;

                        try {
                            $pdf = new ServicioCertificadoPdf();
                            Mail::to($correo)->send(
                                new CertificadoEmitidoMail($record, $pdf->obtenerContenido($record))
                            );

                            Notification::make()
                                ->title('Certificado emitido')
                                ->body("Código: {$record->codigo_verificacion}. Correo enviado a {$correo}.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Certificado emitido (sin correo)')
                                ->body("Código: {$record->codigo_verificacion}. Error al enviar correo: {$e->getMessage()}")
                                ->warning()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('descargar_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn(Certificado $record): bool => $record->estaEmitido())
                    ->url(fn(Certificado $record): string => route('certificados.descargar', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Certificado $record): bool => ! $record->estaAnulado())
                    ->form([
                        Forms\Components\Textarea::make('motivo_anulacion')
                            ->label('Motivo de anulación')
                            ->required()
                            ->rows(3)
                            ->minLength(10)
                            ->maxLength(1000)
                            ->helperText('Describe el error con suficiente detalle para el historial.')
                            ->validationMessages([
                                'required' => 'El motivo de anulación es obligatorio.',
                                'min'      => 'El motivo debe tener al menos 10 caracteres.',
                            ]),
                    ])
                    ->modalHeading('Anular certificado')
                    ->modalSubmitActionLabel('Confirmar anulación')
                    ->action(function (Certificado $record, array $data): void {
                        $record->update([
                            'estado'           => 'anulado',
                            'motivo_anulacion' => $data['motivo_anulacion'],
                        ]);

                        Notification::make()
                            ->title('Certificado anulado')
                            ->body('Motivo registrado correctamente.')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('reemitir')
                    ->label('Reemitir')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(Certificado $record): bool => $record->estaAnulado())
                    ->requiresConfirmation()
                    ->modalHeading('Reemitir certificado')
                    ->modalDescription('Se creará un nuevo certificado en estado pendiente referenciando al anulado. El registro original se conserva para el historial de errores.')
                    ->modalSubmitActionLabel('Confirmar reemisión')
                    ->action(function (Certificado $record): void {
                        Certificado::create([
                            'inscripcion_id'  => $record->inscripcion_id,
                            'estado'          => 'pendiente',
                            'reemitido_de_id' => $record->id,
                        ]);

                        Notification::make()
                            ->title('Certificado reemitido')
                            ->body('Nuevo certificado en estado pendiente. Usa Emitir para generarlo.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('Sin certificados registrados')
            ->emptyStateDescription('Genera el primer certificado con el botón de arriba.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCertificados::route('/'),
            'create' => Pages\CreateCertificado::route('/create'),
            'edit'   => Pages\EditCertificado::route('/{record}/edit'),
        ];
    }
}
