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
        Schema::table('designs', function (Blueprint $table) {
            $table->enum('drawing_approved', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('drawing'); // change position if needed
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->dropColumn('drawing_approved');
        });
    }
};
