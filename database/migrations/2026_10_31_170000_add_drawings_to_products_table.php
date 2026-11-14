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
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'ga_drawing')) {
                    $table->string('ga_drawing')->nullable()->after('productdes');
                }
                if (!Schema::hasColumn('products', 'pit_drawing')) {
                    $table->string('pit_drawing')->nullable()->after('ga_drawing');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'ga_drawing')) {
                    $table->dropColumn('ga_drawing');
                }
                if (Schema::hasColumn('products', 'pit_drawing')) {
                    $table->dropColumn('pit_drawing');
                }
            });
        }
    }
};
