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
        Schema::table('orf', function (Blueprint $table) {
            if (!Schema::hasColumn('orf', 'po_status')) {
                $table->string('po_status')->default('pending')->after('cs_status')->index();
            }
            if (!Schema::hasColumn('orf', 'supplier_po_no')) {
                $table->string('supplier_po_no')->nullable()->after('po_status');
            }
            if (!Schema::hasColumn('orf', 'po_released_at')) {
                $table->timestamp('po_released_at')->nullable()->after('supplier_po_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orf', function (Blueprint $table) {
            $columns = [
                'po_status',
                'supplier_po_no',
                'po_released_at',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orf', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
