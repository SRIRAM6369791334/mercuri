<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotation_term_change_requests', function (Blueprint $table) {
            $table->text('content')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_term_change_requests', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};