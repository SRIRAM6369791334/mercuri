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
        $exists = DB::table('quotation_installation_scopes')->where('name', 'None')->exists();
        if (!$exists) {
            DB::table('quotation_installation_scopes')->insert([
                'name' => 'None',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('quotation_installation_scopes')->where('name', 'None')->delete();
    }
};
