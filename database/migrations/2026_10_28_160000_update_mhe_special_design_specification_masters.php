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
        // 1. Backfill any existing design_specifications with their master spec_name
        $existingSpecs = DB::table('design_specifications')
            ->whereNull('spec_name')
            ->get();

        foreach ($existingSpecs as $spec) {
            $master = DB::table('design_specification_masters')->where('id', $spec->specification)->first();
            if ($master) {
                DB::table('design_specifications')->where('id', $spec->id)->update([
                    'spec_name' => $master->spec_name
                ]);
            }
        }

        // 2. Remove legacy test specifications for MHE Special (category_id = 2, type = 'mhe')
        DB::table('design_specification_masters')
            ->where('category_id', 2)
            ->where('type', 'mhe')
            ->delete();

        // 3. Insert standard MHE Special specifications
        $standardSpecs = [
            'Capacity',
            'Platform Size',
            'Lift Height',
            'Type',
            'Safety Accessories',
            'Beacon',
            'Handrails',
        ];

        $now = now();
        foreach ($standardSpecs as $name) {
            DB::table('design_specification_masters')->insert([
                'category_id' => 2,
                'type'        => 'mhe',
                'spec_name'   => $name,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('design_specification_masters')
            ->where('category_id', 2)
            ->where('type', 'mhe')
            ->delete();

        $legacySpecs = ['A', 'B', 'C', 'D', 'E', 'F', 'capacity'];
        $now = now();
        foreach ($legacySpecs as $name) {
            DB::table('design_specification_masters')->insert([
                'category_id' => 2,
                'type'        => 'mhe',
                'spec_name'   => $name,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
};
