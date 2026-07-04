<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SesionActivaResource\Pages;
use App\Models\SesionActiva;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SesionActivaResource extends Resource
{
    protected static ?string $model = SesionActiva::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Sesiones activas';

    protected static ?string $modelLabel = 'Sesión';

    protected static ?string $pluralModelLabel = 'Sesiones activas';

    protected static ?string $navigationGroup = 'Seguridad';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // Toda visita al sitio (login, verificador público, etc.) crea una fila en
    // "sessions" aunque nadie inicie sesión. Sin este filtro, tráfico anónimo de
    // cualquier país aparecía "en línea" y se veía como si hubieran hackeado la cuenta.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNotNull('user_id');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->placeholder('Sin autenticar')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Dispositivo / navegador')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Última actividad')
                    ->formatStateUsing(fn(int $state): string => \Carbon\Carbon::createFromTimestamp($state)->diffForHumans())
                    ->sortable(),

                Tables\Columns\IconColumn::make('estaEnLinea')
                    ->label('En línea')
                    ->boolean()
                    ->getStateUsing(fn(SesionActiva $record): bool => $record->estaEnLinea()),
            ])
            ->defaultSort('last_activity', 'desc')
            ->actions([
                // "Eliminar" acá = borrar la fila de sessions = forzar logout de ese usuario/dispositivo.
                Tables\Actions\DeleteAction::make()
                    ->label('Cerrar sesión')
                    ->requiresConfirmation()
                    ->modalHeading('Cerrar sesión')
                    ->modalDescription('El usuario será desconectado y deberá volver a iniciar sesión.')
                    ->modalSubmitActionLabel('Cerrar sesión'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Cerrar sesiones seleccionadas'),
                ]),
            ])
            ->emptyStateHeading('No hay sesiones activas')
            ->poll('30s'); // refresca sola, sin recargar la página, pa vigilar en vivo
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSesionesActivas::route('/'),
        ];
    }

    // Las sesiones las crea el login, no un formulario acá.
    public static function canCreate(): bool
    {
        return false;
    }
}
