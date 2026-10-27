<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('freight', function (Blueprint $table) {
        $table->tinyInteger('freight_approve')->default(0)->comment('0 = approved, 1 = not approved');
    });
}

public function down()
{
    Schema::table('freight', function (Blueprint $table) {
        $table->dropColumn('freight_approve');
    });
}
};
