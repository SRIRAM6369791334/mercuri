<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->enum('allowed_discount_status', ['active', 'inactive'])->default('active');
            $table->enum('product_stock_alert_count_status', ['active', 'inactive'])->default('active');
            // Alter gst_validation column type from integer to enum
            DB::statement("ALTER TABLE admin_settings MODIFY gst_validation ENUM('active', 'inactive') DEFAULT 'active'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE admin_settings MODIFY gst_validation INT");
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn([
                'allowed_discount_status',
                'product_stock_alert_count_status',
            ]);
        });
    }
};
