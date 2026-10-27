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
        Schema::table('products', function (Blueprint $table) {
            $table->string('dispatch_time')->nullable()->after('productdes');
        });

        Schema::table('freight', function (Blueprint $table) {
            $table->string('dispatch_time')->nullable()->after('dov');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freight', function (Blueprint $table) {
            $table->dropColumn('dispatch_time');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('dispatch_time');
        });
    }
};
