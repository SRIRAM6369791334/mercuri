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
        Schema::table('vendor_details_tb', function (Blueprint $table) {
            // Change column to NOT NULL
            $table->string('alternate_name')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_details_tb', function (Blueprint $table) {
            // Revert column back to nullable
            $table->string('alternate_name')->nullable(false)->change();
        });
    }
};
