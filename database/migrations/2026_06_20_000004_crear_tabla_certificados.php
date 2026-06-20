<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();
            $table->string('codigo_verificacion', 64)->unique();
            $table->enum('estado', ['pendiente', 'emitido', 'anulado'])->default('pendiente');
            $table->date('fecha_emision')->nullable();
            $table->text('motivo_anulacion')->nullable();
            $table->foreignId('reemitido_de_id')->nullable()->constrained('certificados')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
