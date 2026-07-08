<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $curso_id
 * @property int $participante_id
 * @property string|null $estado_finalizacion
 * @property \Illuminate\Support\Carbon $fecha_inscripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Curso $curso
 * @property-read \App\Models\Participante $participante
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificado> $certificados
 * @property-read \App\Models\Certificado|null $certificadoVigente
 */
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

    /** @return BelongsTo<Curso, $this> */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /** @return BelongsTo<Participante, $this> */
    public function participante(): BelongsTo
    {
        return $this->belongsTo(Participante::class);
    }

    /** @return HasMany<Certificado, $this> */
    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    /** @return HasOne<Certificado, $this> */
    public function certificadoVigente(): HasOne
    {
        return $this->hasOne(Certificado::class)
            ->whereIn('estado', ['pendiente', 'emitido'])
            ->latestOfMany();
    }
}
