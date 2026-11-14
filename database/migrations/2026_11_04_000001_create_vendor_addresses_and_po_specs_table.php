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
        // 1. Create vendor_addresses table if not exists
        if (!Schema::hasTable('vendor_addresses')) {
            Schema::create('vendor_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->string('address_type', 50)->default('Plant / Factory'); // 'Primary Office', 'Plant / Factory', 'Branch Office', 'Warehouse'
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

        // 2. Add technical_specification and additional_file to purchase_order_tb if not exists
        if (Schema::hasTable('purchase_order_tb')) {
            Schema::table('purchase_order_tb', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_order_tb', 'technical_specification')) {
                    $table->text('technical_specification')->nullable()->after('product_description');
                }
                if (!Schema::hasColumn('purchase_order_tb', 'additional_file')) {
                    $table->text('additional_file')->nullable()->after('drawing_attachment');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_order_tb')) {
            Schema::table('purchase_order_tb', function (Blueprint $table) {
                if (Schema::hasColumn('purchase_order_tb', 'technical_specification')) {
                    $table->dropColumn('technical_specification');
                }
                if (Schema::hasColumn('purchase_order_tb', 'additional_file')) {
                    $table->dropColumn('additional_file');
                }
            });
        }
        Schema::dropIfExists('vendor_addresses');
    }
};
