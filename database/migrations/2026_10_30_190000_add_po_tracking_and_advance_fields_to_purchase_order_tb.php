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
            // Branch and Item Identifiers
            if (!Schema::hasColumn('purchase_order_tb', 'mercuriis_branch')) {
                $table->string('mercuriis_branch', 100)->default('Chennai');
            }
            if (!Schema::hasColumn('purchase_order_tb', 'hsn_code')) {
                $table->string('hsn_code', 100)->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'part_no')) {
                $table->string('part_no', 100)->nullable();
            }

            // Advance Financial Fields
            if (!Schema::hasColumn('purchase_order_tb', 'advance_percentage')) {
                $table->decimal('advance_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_order_tb', 'advance_amount')) {
                $table->decimal('advance_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('purchase_order_tb', 'advance_status')) {
                $table->string('advance_status', 50)->default('not_required');
            }

            // Step 1: Sir / Super Admin Approval
            if (!Schema::hasColumn('purchase_order_tb', 'sir_approved_by')) {
                $table->unsignedBigInteger('sir_approved_by')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'sir_approved_at')) {
                $table->timestamp('sir_approved_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'sir_remark')) {
                $table->text('sir_remark')->nullable();
            }

            // Step 2: Finance Disbursement
            if (!Schema::hasColumn('purchase_order_tb', 'finance_released_by')) {
                $table->unsignedBigInteger('finance_released_by')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'finance_released_at')) {
                $table->timestamp('finance_released_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'finance_utr_no')) {
                $table->string('finance_utr_no', 100)->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'finance_payment_mode')) {
                $table->string('finance_payment_mode', 50)->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'finance_remark')) {
                $table->text('finance_remark')->nullable();
            }

            // Production & Logistics Tracking
            if (!Schema::hasColumn('purchase_order_tb', 'calculated_due_date')) {
                $table->date('calculated_due_date')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'supplier_work_order_no')) {
                $table->string('supplier_work_order_no', 100)->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'supplier_dispatch_date')) {
                $table->date('supplier_dispatch_date')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_tb', 'production_milestone')) {
                $table->string('production_milestone', 50)->default('awaiting_advance');
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
                'mercuriis_branch',
                'hsn_code',
                'part_no',
                'advance_percentage',
                'advance_amount',
                'advance_status',
                'sir_approved_by',
                'sir_approved_at',
                'sir_remark',
                'finance_released_by',
                'finance_released_at',
                'finance_utr_no',
                'finance_payment_mode',
                'finance_remark',
                'calculated_due_date',
                'supplier_work_order_no',
                'supplier_dispatch_date',
                'production_milestone'
            ]);
        });
    }
};
