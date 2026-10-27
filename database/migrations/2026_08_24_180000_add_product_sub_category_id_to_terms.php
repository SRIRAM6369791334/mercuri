<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            if (!Schema::hasColumn('terms', 'product_sub_category_id')) {
                $table->unsignedBigInteger('product_sub_category_id')->nullable()->after('product_category_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            if (Schema::hasColumn('terms', 'product_sub_category_id')) {
                $table->dropColumn('product_sub_category_id');
            }
        });
    }
};
