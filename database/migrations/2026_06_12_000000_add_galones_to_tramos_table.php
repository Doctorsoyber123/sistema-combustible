<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tramos', function (Blueprint $table) {
            $table->decimal('galones', 8, 2)->default(0.00)->after('km');
        });
    }

    public function down(): void
    {
        Schema::table('tramos', function (Blueprint $table) {
            $table->dropColumn('galones');
        });
    }
};
