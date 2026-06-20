<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public static function generarCodigoVerificacion(): string
    {
        // Excluye 0/O, 1/I/L para evitar confusión visual al leer el código
        $caracteres = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 8; $i++) {
                $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
        } while (static::where('codigo_verificacion', $codigo)->exists());

        return $codigo;
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function certificadoOriginal(): BelongsTo
    {
        return $this->belongsTo(Certificado::class, 'reemitido_de_id');
    }

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
