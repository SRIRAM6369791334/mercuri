<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add a per-sub-category default installation charge.
     *
     * When set, the quotation form uses this value instead of the
     * global admin_settings.default_installation_charge.  A NULL value
     * means "fall back to the global default".
     *
     * The seeder sets every existing sub-category to 15 000 so that
     * behaviour is identical to the current global default.
     */
    public function up(): void
    {
        Schema::table('product_sub_categories', function (Blueprint $table) {
            $table->decimal('default_installation_charge', 15, 2)
                  ->nullable()
                  ->after('quotation_image');
        });

        // Seed all existing sub-categories with ₹15,000
        DB::table('product_sub_categories')
            ->whereNull('default_installation_charge')
            ->update(['default_installation_charge' => 15000]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sub_categories', function (Blueprint $table) {
            $table->dropColumn('default_installation_charge');
        });
    }
};
