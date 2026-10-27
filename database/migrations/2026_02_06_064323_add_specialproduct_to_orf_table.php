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
        Schema::table('orf', function (Blueprint $table) {
            $table->unsignedBigInteger('specialproduct')
                  ->nullable()
                  ->after('leano'); // change position if needed
        });
    }

    public function down(): void
    {
        Schema::table('orf', function (Blueprint $table) {
            $table->dropColumn('specialproduct');
        });
    }
};
