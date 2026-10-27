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
        if (!Schema::hasTable('stock_movements_tb')){
        Schema::create('stock_movements_tb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendor_details_tb')->onDelete('cascade');
            $table->foreignId("pe_id")->nullable()->constrained("purchase_entry_tb");
            $table->enum('type',['purchase', 'sale', 'adjustment']);
            $table->integer('quantity');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements_tb');
    }
};
