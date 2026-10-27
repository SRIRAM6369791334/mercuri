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
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->nullable()->after('quota_id');
            $table->decimal('tax_total', 15, 2)->nullable()->after('subtotal');
            $table->decimal('grand_total', 15, 2)->nullable()->after('tax_total');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'tax_total', 'grand_total']);
        });
    }
};
