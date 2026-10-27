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
        Schema::create('quotation_status', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert initial data
        DB::table('quotation_status')->insert([
            ['name' => 'Under Technical Discussion', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Under Commercial Discussion', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Under Hold', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Under Postpond', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Under Lost', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_status');
    }
};
