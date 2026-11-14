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
        $now = now();

        $groups = [
            'Hydraulic Scissor Lift',
            'Dock Leveler',
        ];

        foreach ($groups as $group) {
            $exists = DB::table('product_sub_categories')
                ->where('cat_id', 2)
                ->where('subcategory_name', $group)
                ->exists();

            if (!$exists) {
                DB::table('product_sub_categories')->insert([
                    'cat_id'            => 2,
                    'subcategory_name'  => $group,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_sub_categories')
            ->where('cat_id', 2)
            ->whereIn('subcategory_name', ['Hydraulic Scissor Lift', 'Dock Leveler'])
            ->delete();
    }
};
