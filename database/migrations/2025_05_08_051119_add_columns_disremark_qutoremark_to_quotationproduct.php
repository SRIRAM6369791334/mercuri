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
        Schema::table('quotationproduct', function (Blueprint $table) {
            $table->text('disremark')->nullable()->after('dis_app');
            $table->text('qutoremark')->nullable()->after('disremark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotationproduct', function (Blueprint $table) {
            $table->dropColumn(['disremark', 'qutoremark']);
        });
    }
};
