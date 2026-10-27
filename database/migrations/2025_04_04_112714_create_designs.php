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
        if (!Schema::hasTable('designs')){
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->string("idlead",50)->nullable();
            $table->string("idcustomer",50)->nullable();
            $table->string("product")->nullable();
            $table->string("category")->nullable();
            $table->string("ganumber")->nullable();
            $table->integer("quantity")->nullable();
            $table->string("assigndesign")->nullable();
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
