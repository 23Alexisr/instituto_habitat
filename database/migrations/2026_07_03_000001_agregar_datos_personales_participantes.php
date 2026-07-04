<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('correo');
            $table->string('telefono', 20)->nullable()->after('foto');
            $table->date('fecha_nacimiento')->nullable()->after('telefono');
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable()->after('fecha_nacimiento');
            $table->string('direccion')->nullable()->after('genero');
        });
    }

    public function down(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropColumn(['foto', 'telefono', 'fecha_nacimiento', 'genero', 'direccion']);
        });
    }
};
