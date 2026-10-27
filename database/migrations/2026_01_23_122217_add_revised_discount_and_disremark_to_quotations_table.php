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

            $table->decimal('revised_discount', 8, 2)
                  ->nullable()
                  ->after('dis_app'); // change position if needed

            $table->text('disremark')
                  ->nullable()
                  ->after('revised_discount');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['revised_discount', 'disremark']);
        });
    }
};
