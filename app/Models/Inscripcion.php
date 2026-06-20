<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = [
        'curso_id',
        'participante_id',
        'estado_finalizacion',
        'fecha_inscripcion',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class);
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    public function certificadoVigente(): HasOne
    {
        return $this->hasOne(Certificado::class)
            ->whereIn('estado', ['pendiente', 'emitido'])
            ->latestOfMany();
    }
}
