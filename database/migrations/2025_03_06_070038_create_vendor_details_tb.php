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
        Schema::create('vendor_details_tb', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id');
            $table->string('company_name');
            $table->string('alternate_name');
            $table->string('point_of_contact');
            $table->string('contact_number');
            $table->string('email');
            $table->integer('gst_number');
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_details_tb');
    }
};
