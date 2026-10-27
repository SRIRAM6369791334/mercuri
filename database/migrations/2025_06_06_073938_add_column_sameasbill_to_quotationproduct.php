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
            $table->integer("same_as_billing_address")->nullable()->comment('0 - no, 1 - yes');
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
