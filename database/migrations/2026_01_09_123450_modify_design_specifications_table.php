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
        //
        Schema::table('design_specifications', function (Blueprint $table) {
    $table->string('spec_name');   // Load Capacity
    $table->string('spec_value');  // 5 Ton
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
