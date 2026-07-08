<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon $fecha_fin
 * @property string $docente
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inscripcion> $inscripciones
 */
class Curso extends Model
{
    // La tabla se llama 'cursos', aunque Laravel ya la hubiera detectado sola por el nombre del modelo
    protected $table = 'cursos';

    // Estos son los únicos campos que se pueden guardar o actualizar desde un formulario.
    // Si alguien intenta mandar otro campo que no esté aquí (por ejemplo un id manipulado), Laravel lo ignora solo.
    protected $fillable = [
        'codigo',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'docente',
    ];

    // Con esto, cuando lea $curso->fecha_inicio no me llega un texto plano,
    // me llega ya como objeto Carbon( es una librería que convierte tus fechas de texto plano en objetos con métodos listos para formatear,
    //  comparar y calcular diferencias de tiempo sin que tengas que parsear nada a mano.),
    //  así puedo formatear fechas o comparar directo sin parsear nada a mano
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    // Cada vez que se guarda un código de curso, lo paso a mayúsculas y le quito espacios sobrantes.
    // Así no importa cómo lo escriba quien llena el formulario, siempre queda parejo en la base de datos
    protected function codigo(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtoupper(trim($value)),
        );
    }

    // Igual acá, pero para que el nombre del curso quede en formato título
    // (la primera letra de cada palabra en mayúscula)
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8'),
        );
    }

    // Mismo formato título, ahora para el nombre del docente
    protected function docente(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8'),
        );
    }

    // Un curso puede tener varias inscripciones asociadas.
    // Esto asume que en la tabla inscripciones existe una columna curso_id apuntando para acá
    /** @return HasMany<Inscripcion, $this> */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }
}