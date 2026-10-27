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
            $table->string("approval_status", 1)
            ->default('0')
            ->nullable()
            ->comment('0 = pen, 1 = app, 2 = not app')
            ->change();

      $table->string("cs_status", 1)
            ->default('0')
            ->nullable()
            ->comment('0 = pen, 1 = app, 2 = not app')
            ->change();
  });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orf', function (Blueprint $table) {
            $table->string("approval_status")->nullable()->change();
            $table->string("cs_status")->nullable()->change();
        });
    }
};
