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
        Schema::table('freight_category', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id')->nullable()->after('categoty');
        });

        Schema::table('freight_sub_category', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id')->nullable()->after('sub_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freight_sub_category', function (Blueprint $table) {
            $table->dropColumn('product_category_id');
        });

        Schema::table('freight_category', function (Blueprint $table) {
            $table->dropColumn('product_category_id');
        });
    }
};
