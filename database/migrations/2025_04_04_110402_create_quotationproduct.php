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
        if (!Schema::hasTable('quotationproduct')){
        Schema::create('quotationproduct', function (Blueprint $table) {
            $table->id();
            $table->string("leadno")->nullable();
            $table->string("quotationno")->nullable();
            $table->string("cliname")->nullable();
            $table->string("placeofsupply")->nullable();
            $table->string("shippostalcode")->nullable();
            $table->unsignedBigInteger("shcitylist")->nullable();
            $table->unsignedBigInteger("shistate_id")->nullable();
            $table->unsignedBigInteger("shippingcountry")->nullable();
            $table->string("gstnum")->nullable();
            $table->string("catepro")->nullable();
            $table->string("product")->nullable();
            $table->string("part_no")->nullable();
            $table->integer("quanty")->nullable();
            $table->decimal("allowdis",10,2)->nullable();
            $table->decimal("requestdis",10,2)->nullable();
            $table->decimal("ratee",10,2)->nullable();
            $table->decimal("price",10,2)->nullable();
            $table->decimal("priceamt",10,2)->nullable();
            $table->decimal("lbt",10,2)->nullable();
            $table->decimal("tax",10,2)->nullable();
            $table->decimal("taxamt",10,2)->nullable();
            $table->string("grandtotal")->nullable();
            $table->tinyInteger("quto_app")->nullable();
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotationproduct');
    }
};
