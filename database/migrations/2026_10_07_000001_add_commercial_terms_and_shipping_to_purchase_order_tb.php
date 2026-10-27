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
            if (!Schema::hasColumn('purchase_order_tb', 'lead_time')) {
                $table->string('lead_time')->nullable()->after('terms_condition_id');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('lead_time');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'warranty_terms')) {
                $table->text('warranty_terms')->nullable()->after('payment_terms');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'special_instructions')) {
                $table->text('special_instructions')->nullable()->after('warranty_terms');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'shipping_type')) {
                $table->string('shipping_type')->nullable()->default('warehouse')->after('special_instructions');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('shipping_type');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'shipping_gst')) {
                $table->string('shipping_gst')->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'drawing_attachment')) {
                $table->string('drawing_attachment')->nullable()->after('files');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'quote_attachments')) {
                $table->text('quote_attachments')->nullable()->after('drawing_attachment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_tb', function (Blueprint $table) {
            $columns = [
                'lead_time',
                'payment_terms',
                'warranty_terms',
                'special_instructions',
                'shipping_type',
                'shipping_address',
                'shipping_gst',
                'drawing_attachment',
                'quote_attachments',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('purchase_order_tb', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
