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
        Schema::create('freight', function (Blueprint $table) {
            $table->id();
            $table->string('leadnumber')->nullable();
            $table->string('quotationno')->nullable();
            $table->string('termscondition')->nullable();
            $table->date('dov')->nullable();
            $table->string('freight')->nullable();
            $table->string('exwork')->nullable();
            $table->string('freightextra')->nullable();
            $table->string('unloading')->nullable();
            $table->text('remarks')->nullable();
            $table->string('currency')->nullable();
            $table->string('warranty')->nullable();
            $table->string('installation')->nullable();
            $table->string('agent')->nullable();
            $table->decimal('amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freight');
    }
};
