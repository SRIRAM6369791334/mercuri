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
        Schema::table('employeeassets', function (Blueprint $table) {
            $table->string('status')->default('Assigned')->after('remarks'); // You can change default if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('employeeassets', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
