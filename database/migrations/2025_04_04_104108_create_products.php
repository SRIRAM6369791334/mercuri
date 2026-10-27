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
        if (!Schema::hasTable('products')){
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('productname');
                $table->decimal("rate",15, 2);
                $table->string("hsn")->nullable();
                $table->decimal("gst",8, 2)->nullable();
                $table->string("partno")->nullable();
                $table->text("productdes")->nullable();
                $table->string("Productcategory")->nullable();
                $table->integer("quantity");
                $table->string("sku")->nullable();
                $table->string("uom")->nullable();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
