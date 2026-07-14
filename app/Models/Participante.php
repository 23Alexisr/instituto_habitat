<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nombre
 * @property string $dni
 * @property string $correo
 * @property string|null $foto
 * @property string|null $telefono
 * @property \Illuminate\Support\Carbon|null $fecha_nacimiento
 * @property string|null $genero
 * @property string|null $direccion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 */
class Participante extends Model
{
    protected $table = 'participantes';

    protected $fillable = [
        'nombre',
        'dni',
        'correo',
        'foto',
        'telefono',
        'fecha_nacimiento',
        'genero',
        'direccion',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8'),
        );
    }

    protected function dni(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => preg_replace('/\D/', '', $value),
        );
    }

    protected function correo(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtolower(trim($value)),
        );
    }

    // DNI parcialmente oculto para mostrar en contextos públicos ( verificador de certificados).
    // Deja visibles los primeros 3 y últimos 2 dígitos, oculta el resto con asteriscos.
    protected function dniEnmascarado(): Attribute
    {
        return Attribute::make(
            get: function () {
                $dni = $this->dni;
                $longitud = strlen($dni);

                // DNI corto o inválido: no hay suficientes dígitos para dejar 3+2 visibles
                // sin terminar mostrando el DNI completo, mejor ocultarlo entero.
                if ($longitud <= 4) {
                    return str_repeat('*', $longitud);
                }

                // Caso normal (DNI peruano de 8 dígitos): primeros 3 + asteriscos en el medio + últimos 2.
                // La cantidad de asteriscos se calcula segun la longitud real, no queda fijo en 3,
                // para que tambien funcione bien si algun dia hay un DNI de otra longitud.
                return substr($dni, 0, 3) . str_repeat('*', $longitud - 5) . substr($dni, -2);
            },
        );
    }

    /** @return HasMany<Inscripcion, $this> */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }
}
