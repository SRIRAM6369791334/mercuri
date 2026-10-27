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
        Schema::create('assign_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mhe_special_user_id')->nullable();
            $table->unsignedBigInteger('racking_user_id')->nullable();
            $table->unsignedBigInteger('pallet_user_id')->nullable();

            $table->timestamps();

            $table->foreign('mhe_special_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('racking_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('pallet_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_settings');
    }
};
