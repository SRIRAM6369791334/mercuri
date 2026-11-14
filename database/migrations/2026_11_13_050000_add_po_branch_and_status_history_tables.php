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
            if (!Schema::hasColumn('purchase_order_tb', 'supplier_address_id')) {
                $table->unsignedBigInteger('supplier_address_id')->nullable()->after('supplier_gst');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'customer_site_id')) {
                $table->unsignedBigInteger('customer_site_id')->nullable()->after('shipping_gst');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'dispatch_reminder_sent_at')) {
                $table->timestamp('dispatch_reminder_sent_at')->nullable()->after('supplier_dispatch_date');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'po_date')) {
                $table->date('po_date')->nullable()->after('po_no');
            }
        });

        if (!Schema::hasTable('po_status_history')) {
            Schema::create('po_status_history', function (Blueprint $table) {
                $table->id();
                $table->string('po_no')->index();
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->string('action');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_status_history');

        Schema::table('purchase_order_tb', function (Blueprint $table) {
            $colsToDrop = [];
            foreach (['supplier_address_id', 'customer_site_id', 'dispatch_reminder_sent_at', 'po_date'] as $col) {
                if (Schema::hasColumn('purchase_order_tb', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
