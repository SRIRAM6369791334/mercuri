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
        Schema::create('design_inputs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('design_id');
    $table->string('file_path');
    $table->timestamps();

    $table->foreign('design_id')
          ->references('id')->on('designs')
          ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_inputs');
    }
};
