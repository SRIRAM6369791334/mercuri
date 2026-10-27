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
        Schema::create('freight_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freight_cat_id')->constrained('freight_category')->onDelete('cascade');
            $table->foreignId('freight_sub_cat_id')->constrained('freight_sub_category')->onDelete('cascade');
            $table->string('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freight_details');
    }
};
