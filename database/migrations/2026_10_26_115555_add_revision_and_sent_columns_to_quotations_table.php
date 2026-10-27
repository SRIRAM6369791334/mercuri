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
            $table->boolean('sent_to_customer')->default(false)->after('charge_approved_at');
            $table->timestamp('sent_to_customer_at')->nullable()->after('sent_to_customer');
            $table->unsignedBigInteger('sent_to_customer_by')->nullable()->after('sent_to_customer_at');
            $table->integer('revision_major')->default(0)->after('sent_to_customer_by');
            $table->integer('revision_minor')->default(0)->after('revision_major');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'sent_to_customer',
                'sent_to_customer_at',
                'sent_to_customer_by',
                'revision_major',
                'revision_minor',
            ]);
        });
    }
};
