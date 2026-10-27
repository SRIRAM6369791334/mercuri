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
         Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_name');                  // e.g., Laptop, Chair
            $table->string('asset_type')->nullable();      // e.g., Electronics, Furniture
            $table->string('serial_number')->nullable();   // Serial No / Tag ID
            $table->string('assigned_to')->nullable();     // Can link to employee or user ID
            $table->date('purchase_date')->nullable();     // Date of purchase
            $table->decimal('cost', 10, 2)->nullable();     // Purchase cost
            $table->text('description')->nullable();       // Additional details
            $table->string('status')->default('Available'); // e.g., Available, Assigned, Damaged
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
