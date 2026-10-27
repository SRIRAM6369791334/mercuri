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
         Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('department'); // Department name
            $table->time('logintime')->nullable(); // Scheduled login time
            $table->time('logouttime')->nullable(); // Scheduled logout time
            $table->integer('loginbuffer')->default(0); // Buffer in minutes before login
            $table->integer('logoutbuffer')->default(0); // Buffer in minutes after logout
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
