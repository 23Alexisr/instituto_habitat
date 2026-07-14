<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $inscripcion_id
 * @property string $codigo_verificacion
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $fecha_emision
 * @property string|null $motivo_anulacion
 * @property int|null $reemitido_de_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inscripcion $inscripcion
 * @property-read \App\Models\Certificado|null $certificadoOriginal
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificado> $reemisiones
 */
class Certificado extends Model
{
    protected $table = 'certificados';

    protected $fillable = [
        'inscripcion_id',
        'codigo_verificacion',
        'estado',
        'fecha_emision',
        'motivo_anulacion',
        'reemitido_de_id',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Certificado $certificado): void {
            if (empty($certificado->codigo_verificacion)) {
                $certificado->codigo_verificacion = static::generarCodigoVerificacion();
            }
        });
    }

    // Genera un código de verificación único de 8 caracteres para un certificado.
    // No es secuencial a propósito: un código adivinable (1, 2, 3...) permitiría
    // a cualquiera enumerar certificados de otras personas cambiando un número en la URL.
    public static function generarCodigoVerificacion(): string
    {
        // Excluye 0/O, 1/I/L para evitar confusión visual al leer el código.
        // El código se imprime en el certificado físico y alguien lo va a tipear a mano
        // para verificarlo, por eso esto es una decisión de UX, no solo de seguridad.
        $caracteres = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        // do-while porque generamos al azar (con random_int, criptográficamente seguro,
        // no rand()): en teoría puede haber colisión con un código ya existente, aunque
        // con 31^8 combinaciones posibles es extremadamente improbable en la práctica.
        // El loop es lo único que garantiza unicidad de verdad, no solo "probablemente único".
        do {
            $codigo = '';
            for ($i = 0; $i < 8; $i++) {
                $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
        } while (static::where('codigo_verificacion', $codigo)->exists());

        return $codigo;
    }

    /** @return BelongsTo<Inscripcion, $this> */
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    /** @return BelongsTo<Certificado, $this> */
    public function certificadoOriginal(): BelongsTo
    {
        return $this->belongsTo(Certificado::class, 'reemitido_de_id');
    }

    /** @return HasMany<Certificado, $this> */
    public function reemisiones(): HasMany
    {
        return $this->hasMany(Certificado::class, 'reemitido_de_id');
    }

    public function estaAnulado(): bool
    {
        return $this->estado === 'anulado';
    }

    public function estaEmitido(): bool
    {
        return $this->estado === 'emitido';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }
}
