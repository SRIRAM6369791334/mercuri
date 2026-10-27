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
       Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('secondname')->nullable();
    $table->date('dob');
    $table->string('qualification');
    $table->string('mobile_no', 15);
    $table->string('email')->nullable();
    $table->string('nationality');
    $table->date('doj');
    $table->string('gender');
    $table->unsignedBigInteger('designation_id')->nullable(); // foreign key if needed
    $table->string('oridesignation')->nullable();
    $table->string('salary')->nullable();
    $table->string('bloodgroup')->nullable();
    $table->text('temp_address')->nullable();
    $table->text('per_address')->nullable();
    $table->boolean('employestat')->default(0); // 0: Active, 1: Inactive
    $table->tinyInteger('maritalstatus')->nullable(); // 0: none, 1: single, 2: married
    $table->string('photo')->nullable(); // for uploaded photo
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
