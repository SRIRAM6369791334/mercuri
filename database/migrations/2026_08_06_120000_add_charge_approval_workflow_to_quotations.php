<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->decimal('default_installation_charge', 15, 2)->default(0)->after('gst_validation');
            $table->decimal('default_freight_charge', 15, 2)->default(0)->after('default_installation_charge');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('default_installation_charge', 15, 2)->default(0)->after('grand_total');
            $table->decimal('installation_charge', 15, 2)->default(0)->after('default_installation_charge');
            $table->decimal('default_freight_charge', 15, 2)->default(0)->after('installation_charge');
            $table->decimal('freight_charge', 15, 2)->default(0)->after('default_freight_charge');
            $table->string('charge_approval_status')->default('Approved')->after('freight_charge');
            $table->unsignedBigInteger('charge_approved_by')->nullable()->after('charge_approval_status');
            $table->timestamp('charge_approved_at')->nullable()->after('charge_approved_by');
        });

        Schema::create('quotation_charge_approval_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('action');
            $table->string('status');
            $table->decimal('default_installation_charge', 15, 2);
            $table->decimal('installation_charge', 15, 2);
            $table->decimal('default_freight_charge', 15, 2);
            $table->decimal('freight_charge', 15, 2);
            $table->foreignId('acted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_charge_approval_histories');
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['default_installation_charge', 'installation_charge', 'default_freight_charge', 'freight_charge', 'charge_approval_status', 'charge_approved_by', 'charge_approved_at']);
        });
        Schema::table('admin_settings', function (Blueprint $table) {
            $table->dropColumn(['default_installation_charge', 'default_freight_charge']);
        });
    }
};
