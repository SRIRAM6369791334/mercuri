<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Seeds/repairs the admin_settings default charge values. The earlier
     * migration (2026_08_06_120000) added these columns with a DB default
     * of 0, so the single admin_settings row is stuck at 0.00/0.00 until
     * someone edits it. This migration sets the intended business defaults
     * (Installation: 10,000 / Freight: 5,000) once, permanently — no manual
     * tinker step needed on any environment.
     */
    public function up(): void
    {
        $settings = DB::table('admin_settings')->first();

        if ($settings) {
            DB::table('admin_settings')
                ->where('id', $settings->id)
                ->update([
                    'default_installation_charge' => 15000,
                    'default_freight_charge' => 5000,
                ]);
        } else {
            DB::table('admin_settings')->insert([
                'default_installation_charge' => 15000,
                'default_freight_charge' => 5000,
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
        DB::table('admin_settings')->update([
            'default_installation_charge' => 0,
            'default_freight_charge' => 0,
        ]);
    }
};