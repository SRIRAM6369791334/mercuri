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

            $table->decimal('basic_total', 15, 2)
                  ->nullable()
                  ->after('quota_id');

            $table->decimal('allow_dis', 5, 2)
                  ->nullable()
                  ->comment('Allowed discount percentage');

            $table->decimal('req_dis', 5, 2)
                  ->nullable()
                  ->comment('Requested discount percentage');

            $table->decimal('reqdis_amt', 15, 2)
                  ->nullable()
                  ->comment('Requested discount amount');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'basic_total',
                'allow_dis',
                'req_dis',
                'reqdis_amt'
            ]);
        });
    }
};
