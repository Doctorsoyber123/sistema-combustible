<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boleta_consumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumo_id')->constrained('consumos')->cascadeOnDelete();
            $table->foreignId('boleta_id')->constrained('boletas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['consumo_id', 'boleta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boleta_consumo');
    }
};
