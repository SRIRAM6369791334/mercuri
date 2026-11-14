<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add division column to leads table if it doesn't exist
        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'division')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('division', 50)->nullable()->after('category');
            });
        }

        // 2. Ensure product_categories_tb has the required segments for Pallet and Racking
        if (Schema::hasTable('product_categories_tb')) {
            $now = now();

            // Metal Pallet Standard
            if (!DB::table('product_categories_tb')->where('category_name', 'Metal Pallet Standard')->exists()) {
                DB::table('product_categories_tb')->insert([
                    'category_name' => 'Metal Pallet Standard',
                    'type'          => 'pallet',
                    'sub_category'  => 'standard',
                    'dispatch_time' => '10-15 Days',
                    'vendor_id'     => 0,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }

            // Metal Pallet Special
            if (!DB::table('product_categories_tb')->where('category_name', 'Metal Pallet Special')->exists()) {
                DB::table('product_categories_tb')->insert([
                    'category_name' => 'Metal Pallet Special',
                    'type'          => 'pallet',
                    'sub_category'  => 'special',
                    'dispatch_time' => '15-20 Days',
                    'vendor_id'     => 0,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }

            // Racking Special
            if (!DB::table('product_categories_tb')->where('category_name', 'Racking Special')->exists()) {
                DB::table('product_categories_tb')->insert([
                    'category_name' => 'Racking Special',
                    'type'          => 'rack',
                    'sub_category'  => 'special',
                    'dispatch_time' => '20-25 Days',
                    'vendor_id'     => 0,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'division')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('division');
            });
        }

        if (Schema::hasTable('product_categories_tb')) {
            DB::table('product_categories_tb')
                ->whereIn('category_name', ['Metal Pallet Standard', 'Metal Pallet Special', 'Racking Special'])
                ->delete();
        }
    }
};
