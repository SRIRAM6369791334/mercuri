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
        Schema::table('freight', function (Blueprint $table) {
            $table->foreignId('freight_detail_id')->constrained('freight_details')->nullable()->after('dov');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freight', function (Blueprint $table) {
            //
        });
    }
};
