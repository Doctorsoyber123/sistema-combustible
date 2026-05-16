<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_boleta');                 // N de boleta
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->string('proveedor')->nullable();         // Grifo / estacion
            $table->decimal('galones', 8, 2);
            $table->decimal('precio_galon', 8, 2);           // Precio unitario
            $table->decimal('total', 10, 2);                 // Calculado: galones x precio
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletas');
    }
};
