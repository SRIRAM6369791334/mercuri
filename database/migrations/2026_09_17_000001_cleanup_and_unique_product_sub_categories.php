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
        // 1. Dynamic cleanup of existing duplicate subcategories
        $duplicates = DB::table('product_sub_categories')
            ->select(
                'cat_id',
                DB::raw("LOWER(TRIM(REPLACE(subcategory_name, ' ', ''))) as norm_name"),
                DB::raw('GROUP_CONCAT(id ORDER BY id ASC) as ids')
            )
            ->groupBy('cat_id', DB::raw("LOWER(TRIM(REPLACE(subcategory_name, ' ', '')))"))
            ->havingRaw('count(*) > 1')
            ->get();

        foreach ($duplicates as $group) {
            $ids = explode(',', $group->ids);
            $masterId = (int)$ids[0];
            $dupeIds = array_map('intval', array_slice($ids, 1));

            // Remap products
            if (Schema::hasTable('products') && Schema::hasColumn('products', 'sub_cat_id')) {
                DB::table('products')->whereIn('sub_cat_id', $dupeIds)->update(['sub_cat_id' => $masterId]);
            }

            // Remap terms
            if (Schema::hasTable('terms') && Schema::hasColumn('terms', 'product_sub_category_id')) {
                DB::table('terms')->whereIn('product_sub_category_id', $dupeIds)->update(['product_sub_category_id' => $masterId]);
            }

            // Remap leads
            if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'subcategory_id')) {
                DB::table('leads')->whereIn('subcategory_id', $dupeIds)->update(['subcategory_id' => $masterId]);
            }

            // Delete redundant subcategories
            DB::table('product_sub_categories')->whereIn('id', $dupeIds)->delete();
        }

        // 2. Also ensure specific known duplicates are normalized & merged if casing differed
        $knownMerges = [
            // Cat 4: MHE High End
            ['cat_id' => 4, 'name' => 'Reach Truck', 'patterns' => ['reach truck', 'reachtruck']],
            ['cat_id' => 4, 'name' => 'Electric Forklift', 'patterns' => ['electric forklift', 'electricforklift']],
            ['cat_id' => 4, 'name' => 'Walkie Electric Pallet Truck', 'patterns' => ['walkie electric pallet truck', 'walkieelectricpallettruck']],
        ];

        foreach ($knownMerges as $km) {
            $catId = $km['cat_id'];
            $target = DB::table('product_sub_categories')
                ->where('cat_id', $catId)
                ->where(function ($q) use ($km) {
                    foreach ($km['patterns'] as $p) {
                        $q->orWhereRaw("LOWER(TRIM(REPLACE(subcategory_name, ' ', ''))) = ?", [str_replace(' ', '', $p)]);
                    }
                })
                ->orderBy('id', 'asc')
                ->first();

            if ($target) {
                // Update name to standard casing
                DB::table('product_sub_categories')
                    ->where('id', $target->id)
                    ->update(['subcategory_name' => $km['name']]);

                // Find any other rows matching these patterns
                $otherIds = DB::table('product_sub_categories')
                    ->where('cat_id', $catId)
                    ->where('id', '!=', $target->id)
                    ->where(function ($q) use ($km) {
                        foreach ($km['patterns'] as $p) {
                            $q->orWhereRaw("LOWER(TRIM(REPLACE(subcategory_name, ' ', ''))) = ?", [str_replace(' ', '', $p)]);
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                if (!empty($otherIds)) {
                    if (Schema::hasTable('products') && Schema::hasColumn('products', 'sub_cat_id')) {
                        DB::table('products')->whereIn('sub_cat_id', $otherIds)->update(['sub_cat_id' => $target->id]);
                    }
                    if (Schema::hasTable('terms') && Schema::hasColumn('terms', 'product_sub_category_id')) {
                        DB::table('terms')->whereIn('product_sub_category_id', $otherIds)->update(['product_sub_category_id' => $target->id]);
                    }
                    if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'subcategory_id')) {
                        DB::table('leads')->whereIn('subcategory_id', $otherIds)->update(['subcategory_id' => $target->id]);
                    }
                    DB::table('product_sub_categories')->whereIn('id', $otherIds)->delete();
                }
            }
        }

        // 3. Add Unique Index if it doesn't already exist
        $existingIndex = collect(DB::select("SHOW INDEX FROM product_sub_categories WHERE Key_name = 'unique_cat_subcat'"))->isNotEmpty();
        if (!$existingIndex) {
            Schema::table('product_sub_categories', function (Blueprint $table) {
                $table->unique(['cat_id', 'subcategory_name'], 'unique_cat_subcat');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $existingIndex = collect(DB::select("SHOW INDEX FROM product_sub_categories WHERE Key_name = 'unique_cat_subcat'"))->isNotEmpty();
        if ($existingIndex) {
            Schema::table('product_sub_categories', function (Blueprint $table) {
                $table->dropUnique('unique_cat_subcat');
            });
        }
    }
};
