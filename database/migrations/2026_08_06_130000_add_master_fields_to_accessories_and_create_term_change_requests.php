<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id')->nullable()->after('product_id');
            $table->string('part_no')->nullable()->after('name');
            $table->string('hsn', 30)->nullable()->after('part_no');
            $table->index('product_category_id');
        });

        Schema::create('quotation_term_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->enum('type', ['payment_term', 'warranty']);
            $table->unsignedBigInteger('old_term_id')->nullable();
            $table->unsignedBigInteger('new_term_id')->nullable();
            $table->string('status')->default('Pending Approval');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['quotation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_term_change_requests');
        Schema::table('accessories', function (Blueprint $table) {
            $table->dropIndex(['product_category_id']);
            $table->dropColumn(['product_category_id', 'part_no', 'hsn']);
        });
    }
};
