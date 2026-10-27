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
        Schema::table('purchase_entry_tb', function (Blueprint $table) {
            $table->decimal('sub_total', 15, 2)->change();
            $table->decimal('total_amount', 15, 2)->change();
            $table->decimal('unit_price', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_entry_tb', function (Blueprint $table) {
            $table->double('sub_total', 8, 2)->change();
            $table->double('total_amount', 8, 2)->change();
            $table->integer('unit_price')->change();
        });
    }
};
