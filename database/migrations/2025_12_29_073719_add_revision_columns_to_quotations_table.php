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

            // Revision number (R0, R1, R2...)
            $table->unsignedInteger('revision_no')
                  ->default(0)
                  ->after('quota_id');

            // Parent quotation (original R0)
            $table->unsignedBigInteger('parent_quotation_id')
                  ->nullable()
                  ->after('revision_no');

            // Optional foreign key (recommended)
            $table->foreign('parent_quotation_id')
                  ->references('id')
                  ->on('quotations')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {

            $table->dropForeign(['parent_quotation_id']);
            $table->dropColumn(['revision_no', 'parent_quotation_id']);
        });
    }
};
