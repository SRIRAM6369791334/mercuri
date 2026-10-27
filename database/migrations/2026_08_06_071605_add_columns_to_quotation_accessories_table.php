<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('quotation_id')->nullable()->after('id');
            // Not a strict foreign key (table name for QuotationProduct model may vary),
            // just an indexed link back to the specific product row this accessory belongs to.
            $table->unsignedBigInteger('quotationproduct_id')->nullable()->after('quotation_id');
            $table->unsignedBigInteger('leadno')->nullable()->after('quotationproduct_id');

            $table->foreignId('accessory_id')->nullable()->after('leadno')
                ->constrained('accessories')->nullOnDelete();

            $table->string('quotationno')->nullable()->after('accessory_id');
            $table->string('name')->nullable()->after('quotationno');
            $table->string('part_no')->nullable()->after('name');
            $table->string('hsn')->nullable()->after('part_no');
            $table->decimal('price', 10, 2)->nullable()->default(0)->after('hsn');
            $table->integer('quantity')->nullable()->default(1)->after('price');
            $table->decimal('total', 10, 2)->nullable()->default(0)->after('quantity');

            $table->index('quotation_id');
            $table->index('quotationproduct_id');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_accessories', function (Blueprint $table) {
            $table->dropForeign(['accessory_id']);
            $table->dropColumn([
                'quotation_id',
                'quotationproduct_id',
                'leadno',
                'accessory_id',
                'quotationno',
                'name',
                'part_no',
                'hsn',
                'price',
                'quantity',
                'total',
            ]);
        });
    }
};