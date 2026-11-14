<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update default installation charges for Goods Lift (₹30,000), Scissor Lift (₹20,000), and Dock Leveler (₹15,000)
        DB::table('product_sub_categories')
            ->where('subcategory_name', 'like', '%Goods Lift%')
            ->orWhere('id', 24)
            ->update(['default_installation_charge' => 30000.00]);

        DB::table('product_sub_categories')
            ->where('subcategory_name', 'like', '%Scissor%')
            ->orWhere('id', 51)
            ->update(['default_installation_charge' => 20000.00]);

        DB::table('product_sub_categories')
            ->where('subcategory_name', 'like', '%Dock%')
            ->orWhereIn('id', [10, 52])
            ->update(['default_installation_charge' => 15000.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_sub_categories')
            ->where('subcategory_name', 'like', '%Scissor%')
            ->update(['default_installation_charge' => 15000.00]);
    }
};
