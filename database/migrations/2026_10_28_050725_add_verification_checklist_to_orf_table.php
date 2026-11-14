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
        Schema::table('orf', function (Blueprint $table) {
            $table->boolean('check_po')->default(0)->after('podate');
            $table->boolean('check_drawing')->default(0)->after('check_po');
            $table->boolean('check_quotation')->default(0)->after('check_drawing');
            $table->boolean('check_orf')->default(0)->after('check_quotation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orf', function (Blueprint $table) {
            $table->dropColumn(['check_po', 'check_drawing', 'check_quotation', 'check_orf']);
        });
    }
};
