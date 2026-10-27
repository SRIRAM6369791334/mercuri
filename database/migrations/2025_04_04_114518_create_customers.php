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
        if (!Schema::hasTable('customers')){
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('cusid')->nullable();
            $table->string('customername')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('cus_email')->nullable();
            $table->string('country_code',10)->nullable();
            $table->string('contact_no',20)->nullable();
            $table->string('designation',50)->nullable();
            $table->string('department',50)->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postalcode',10)->nullable();
            $table->string('gstno')->nullable();
            $table->tinyInteger('soft_d')->default(0);
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
