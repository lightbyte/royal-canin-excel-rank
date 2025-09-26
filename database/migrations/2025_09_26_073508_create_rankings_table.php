<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->index(); // Código único de la clínica
            $table->string('email'); // Email de contacto de la clínica
            $table->integer('recomendaciones')->default(0); // Puntos acumulados
            $table->integer('posicion_actual')->nullable(); // Posición en el ranking actual
            $table->integer('posicion_anterior')->nullable(); // Posición en el ranking anterior
            $table->integer('variacion')->nullable(); // Diferencia de posiciones (+/-)
            $table->string('semana'); // Identificador de la semana
            $table->boolean('activo')->default(true); // Para marcar clínicas inactivas
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['semana', 'activo']);
            $table->index('posicion_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
