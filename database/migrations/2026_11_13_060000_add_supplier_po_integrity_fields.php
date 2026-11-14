<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateVendorCodes = DB::table('vendor_details_tb')
            ->whereNotNull('vendor_id')
            ->select('vendor_id')
            ->groupBy('vendor_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('vendor_id');
        if ($duplicateVendorCodes->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot enforce unique supplier codes; resolve duplicate vendor_id values first: ' .
                $duplicateVendorCodes->implode(', ')
            );
        }

        Schema::table('vendor_details_tb', function (Blueprint $table) {
            $table->unique('vendor_id', 'vendor_details_vendor_id_unique');
        });

        Schema::table('vendor_addresses', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('purchase_order_tb', function (Blueprint $table) {
            $table->decimal('gst_percentage', 5, 2)->nullable();
            $table->decimal('overall_discount_percentage', 5, 2)->default(0);
            $table->decimal('overall_discount_amount', 14, 2)->default(0);
            $table->decimal('taxable_amount', 14, 2)->nullable();
            $table->date('committed_dispatch_date')->nullable();
            $table->date('actual_dispatch_date')->nullable();
            $table->string('lifecycle_status', 30)->default('active')->index();
        });

        Schema::create('po_line_accessories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_line_id')->index();
            $table->unsignedBigInteger('accessory_id')->nullable()->index();
            $table->string('name');
            $table->string('part_no')->nullable();
            $table->string('hsn')->nullable();
            $table->decimal('unit_price', 14, 2);
            $table->decimal('quantity', 12, 3);
            $table->decimal('total', 14, 2);
            $table->timestamps();

            $table->foreign('purchase_order_line_id', 'po_line_accessories_line_fk')
                ->references('id')->on('purchase_order_tb')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_line_accessories');

        Schema::table('purchase_order_tb', function (Blueprint $table) {
            $table->dropColumn([
                'gst_percentage',
                'overall_discount_percentage',
                'overall_discount_amount',
                'taxable_amount',
                'committed_dispatch_date',
                'actual_dispatch_date',
                'lifecycle_status',
            ]);
        });

        Schema::table('vendor_addresses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('vendor_details_tb', function (Blueprint $table) {
            $table->dropUnique('vendor_details_vendor_id_unique');
        });
    }
};
