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
        Schema::create('employee_advances', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('employee_id');
    $table->decimal('amount', 10, 2);
    $table->string('reason')->nullable();
    $table->date('advance_date')->nullable();
    $table->enum('status', ['Given', 'Deducted', 'Settled'])->default('Given');
    $table->timestamps();

    $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_advances');
    }
};
