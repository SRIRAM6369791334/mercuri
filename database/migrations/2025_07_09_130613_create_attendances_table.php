<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date')->default(DB::raw('CURRENT_DATE'));
            $table->timestamp('punch_in_time')->nullable();
            $table->string('punch_in_image')->nullable(); // Store face login image filename
            $table->string('punch_in_location')->nullable();

            $table->timestamp('punch_out_time')->nullable();
            $table->string('punch_out_image')->nullable();
            $table->string('punch_out_location')->nullable();

            $table->time('total_work_duration')->nullable(); // Optional: HH:MM:SS format
            $table->timestamps();

            // Foreign key to employees
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
