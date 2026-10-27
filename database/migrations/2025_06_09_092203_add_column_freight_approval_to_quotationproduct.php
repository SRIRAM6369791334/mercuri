<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotationproduct', function (Blueprint $table) {
            $table->integer('freight_approval')->nullable()->after('dis_app')->comment("0-approved, 1-not approved");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotationproduct', function (Blueprint $table) {
            //
        });
    }
};
