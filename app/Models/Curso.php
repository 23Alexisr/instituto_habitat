<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'cursos';

    protected $fillable = [
        'codigo',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'docente',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    protected function codigo(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtoupper(trim($value)),
        );
    }

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8'),
        );
    }

    protected function docente(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8'),
        );
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }
}
