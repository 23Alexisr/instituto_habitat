<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo de solo lectura/borrado sobre la tabla "sessions" que ya usa el
// SESSION_DRIVER=database de Laravel. No es una tabla propia del dominio,
// por eso el nombrado en inglés y sin timestamps/incrementing (no los tiene).
class SesionActiva extends Model
{
    protected $table = 'sessions';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $casts = [
        'last_activity' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Umbral de 5 min: coincide con el intervalo de poll('30s') del resource,
    // da margen para no marcar "offline" a alguien que sigue activo entre polls.
    public function estaEnLinea(): bool
    {
        return $this->last_activity >= now()->subMinutes(5)->timestamp;
    }
}
