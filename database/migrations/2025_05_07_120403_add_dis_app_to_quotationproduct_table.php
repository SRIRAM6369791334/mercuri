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
            $table->string('dis_app')->nullable()->after('quto_app');
            // Replace 'existing_column_name' with the column after which you want to add 'dis_app'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotationproduct', function (Blueprint $table) {
            $table->dropColumn('dis_app');
        });
    }
};
