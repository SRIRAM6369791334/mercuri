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
        Schema::create('purchase_entry_tb', function (Blueprint $table) {
            $table->id();
            $table->integer("cat_id");
            $table->integer("vendor_id");
            $table->integer("product_id");
            $table->integer("requested_qty");
            $table->integer("received_qty");
            $table->integer("pending_qty");
            $table->integer("unit_price");
            $table->integer("product_total_price");
            $table->float("gst_amount");
            $table->float("sub_total");
            $table->float("total_amount");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_entry_tb');
    }
};
