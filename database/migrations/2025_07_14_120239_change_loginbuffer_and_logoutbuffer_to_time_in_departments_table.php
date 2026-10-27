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
        Schema::table('departments', function (Blueprint $table) {
            $table->time('loginbuffer')->nullable()->change();
            $table->time('logoutbuffer')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->integer('loginbuffer')->default(0)->change();
            $table->integer('logoutbuffer')->default(0)->change();
        });
    }
};
