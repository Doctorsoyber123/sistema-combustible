<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            // Ruta del archivo de evidencia (imagen o PDF de la boleta fisica)
            $table->string('evidencia')->nullable()->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropColumn('evidencia');
        });
    }
};
