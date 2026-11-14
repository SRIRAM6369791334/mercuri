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
        if (!Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->unsignedBigInteger('lead_id')->nullable()->index();
                $table->string('address_type', 50)->default('Plant / Delivery Address'); // 'Primary Office', 'Plant / Factory', 'Branch Office', 'Warehouse', 'Site / Delivery Address'
                $table->text('address');
                $table->string('gst_number', 50)->nullable();
                $table->integer('country_id')->default(76);
                $table->integer('state_id')->nullable();
                $table->integer('city_id')->nullable();
                $table->string('postal_code', 20)->nullable();
                $table->string('contact_person', 100)->nullable();
                $table->string('contact_number', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
