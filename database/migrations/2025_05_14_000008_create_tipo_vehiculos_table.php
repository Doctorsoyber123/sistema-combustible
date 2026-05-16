<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();   // Ej: Retroexcavadora, Camion, Grua...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_vehiculos');
    }
};
