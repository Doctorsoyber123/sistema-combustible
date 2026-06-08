<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumos', function (Blueprint $table) {
            $table->foreignId('boleta_id')->nullable()->constrained('boletas')->nullOnDelete()->after('observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('consumos', function (Blueprint $table) {
            $table->dropForeign(['boleta_id']);
            $table->dropColumn('boleta_id');
        });
    }
};
