<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'additional_warranty_months')) {
                $table->integer('additional_warranty_months')->default(0)->after('freight_charge');
            }
            if (!Schema::hasColumn('quotations', 'additional_warranty_percentage')) {
                $table->decimal('additional_warranty_percentage', 5, 2)->default(0.00)->after('additional_warranty_months');
            }
            if (!Schema::hasColumn('quotations', 'additional_warranty_charge')) {
                $table->decimal('additional_warranty_charge', 15, 2)->default(0.00)->after('additional_warranty_percentage');
            }
            if (!Schema::hasColumn('quotations', 'include_warranty_in_basic')) {
                $table->boolean('include_warranty_in_basic')->default(false)->after('additional_warranty_charge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'additional_warranty_months',
                'additional_warranty_percentage',
                'additional_warranty_charge',
                'include_warranty_in_basic',
            ]);
        });
    }
};
