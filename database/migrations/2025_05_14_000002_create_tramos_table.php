<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');          // Ej: Cantera - Planta
            $table->string('origen');          // Punto A
            $table->string('destino');         // Punto B
            $table->decimal('km', 8, 2);       // Distancia en kilometros
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramos');
    }
};
