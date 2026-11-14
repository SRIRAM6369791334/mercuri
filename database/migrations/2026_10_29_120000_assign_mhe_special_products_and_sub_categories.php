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
        // 1. Assign Hydraulic Goods Lift (Product 187) to MHE Special (Cat 2) & SubCat 24
        DB::table('products')
            ->where('id', 187)
            ->update([
                'Productcategory' => 2,
                'sub_cat_id'      => 24,
                'updated_at'      => now(),
            ]);

        // 2. Assign Hydraulic Scissor Lift (Product 188) to MHE Special (Cat 2) & SubCat 40
        DB::table('products')
            ->where('id', 188)
            ->update([
                'Productcategory' => 2,
                'sub_cat_id'      => 40,
                'updated_at'      => now(),
            ]);

        // 3. Assign Special Dock Levelers (Products 44..48) to SubCat 41 (Dock Leveler under MHE Special)
        DB::table('products')
            ->whereIn('id', [44, 45, 46, 47, 48])
            ->update([
                'Productcategory' => 2,
                'sub_cat_id'      => 41,
                'updated_at'      => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')
            ->where('id', 187)
            ->update([
                'Productcategory' => 10,
                'sub_cat_id'      => 35,
                'updated_at'      => now(),
            ]);

        DB::table('products')
            ->where('id', 188)
            ->update([
                'Productcategory' => 11,
                'sub_cat_id'      => 36,
                'updated_at'      => now(),
            ]);

        DB::table('products')
            ->whereIn('id', [44, 45, 46, 47, 48])
            ->update([
                'sub_cat_id' => null,
                'updated_at' => now(),
            ]);
    }
};
