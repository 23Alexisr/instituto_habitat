<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participante extends Model
{
    protected $table = 'participantes';

    protected $fillable = [
        'nombre',
        'dni',
        'correo',
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

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }
}
