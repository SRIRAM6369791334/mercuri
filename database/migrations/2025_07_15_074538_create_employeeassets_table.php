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
        Schema::create('employeeassets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // refers to employees.id
            $table->unsignedBigInteger('asset_id');    // refers to assets.id
            $table->integer('quantity')->default(1);
            $table->date('assigned_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeeassets');
    }
};
