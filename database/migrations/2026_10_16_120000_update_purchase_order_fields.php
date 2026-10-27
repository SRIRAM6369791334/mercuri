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
        Schema::table('purchase_order_tb', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_tb', 'supplier_billing_address')) {
                $table->text('supplier_billing_address')->nullable()->after('vendor_id');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'supplier_gst')) {
                $table->string('supplier_gst')->nullable()->after('supplier_billing_address');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'discount_percentage')) {
                $table->decimal('discount_percentage', 8, 2)->default(0)->after('product_total_price');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_percentage');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'freight_charges')) {
                $table->decimal('freight_charges', 12, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'unloading_charges')) {
                $table->decimal('unloading_charges', 12, 2)->default(0)->after('freight_charges');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'installation_scope')) {
                $table->string('installation_scope')->nullable()->after('warranty_terms');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'hide_brand_name')) {
                $table->boolean('hide_brand_name')->default(0)->after('installation_scope');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_tb', function (Blueprint $table) {
            $table->dropColumn([
                'supplier_billing_address',
                'supplier_gst',
                'discount_percentage',
                'discount_amount',
                'freight_charges',
                'unloading_charges',
                'installation_scope',
                'hide_brand_name'
            ]);
        });
    }
};
