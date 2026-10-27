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
            if (!Schema::hasColumn('purchase_order_tb', 'orf_id')) {
                $table->unsignedBigInteger('orf_id')->nullable()->after('po_no')->index();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'quotation_id')) {
                $table->unsignedBigInteger('quotation_id')->nullable()->after('orf_id');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'original_order_price')) {
                $table->decimal('original_order_price', 15, 2)->nullable()->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'original_total_price')) {
                $table->decimal('original_total_price', 15, 2)->nullable()->default(0)->after('product_total_price');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'price_variance')) {
                $table->decimal('price_variance', 15, 2)->nullable()->default(0)->after('original_total_price');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'variance_percentage')) {
                $table->decimal('variance_percentage', 8, 2)->nullable()->default(0)->after('price_variance');
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
                'orf_id',
                'quotation_id',
                'original_order_price',
                'original_total_price',
                'price_variance',
                'variance_percentage',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('purchase_order_tb', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
