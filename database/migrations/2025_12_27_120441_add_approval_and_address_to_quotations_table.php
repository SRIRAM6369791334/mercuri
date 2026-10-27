<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('quotations', function (Blueprint $table) {
        $table->tinyInteger('dis_app')->nullable()->after('grand_total');
        $table->tinyInteger('quto_app')->nullable()->after('dis_app');
        $table->tinyInteger('freight_approval')->nullable()->after('quto_app');
        $table->boolean('same_as_billing_address')->default(0)->after('freight_approval');

        // 🔥 SHIPPING / BILLING DETAILS (MOVE FROM quotation_products)
        $table->text('billing_address')->nullable();
        $table->text('shipping_address')->nullable();
        $table->unsignedBigInteger('billing_country')->nullable();
        $table->unsignedBigInteger('shipping_country')->nullable();
        $table->unsignedBigInteger('billing_state')->nullable();
        $table->unsignedBigInteger('shipping_state')->nullable();
        $table->unsignedBigInteger('billing_city')->nullable();
        $table->unsignedBigInteger('shipping_city')->nullable();
        $table->string('billing_pincode', 20)->nullable();
        $table->string('shipping_pincode', 20)->nullable();
        $table->string('billing_gst', 30)->nullable();
        $table->string('shipping_gst', 30)->nullable();
    });
}

public function down()
{
    Schema::table('quotations', function (Blueprint $table) {
        $table->dropColumn([
            'dis_app',
            'quto_app',
            'freight_approval',
            'same_as_billing_address',
            'billing_address',
            'shipping_address',
            'billing_country',
            'shipping_country',
            'billing_state',
            'shipping_state',
            'billing_city',
            'shipping_city',
            'billing_pincode',
            'shipping_pincode',
            'billing_gst',
            'shipping_gst',
        ]);
    });
}

};
