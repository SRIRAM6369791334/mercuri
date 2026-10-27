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
    Schema::table('design_drawings', function (Blueprint $table) {
        $table->string('sales_attach')->nullable()->after('drawing_remarks');
    });
}

public function down()
{
    Schema::table('design_drawings', function (Blueprint $table) {
        $table->dropColumn('sales_attach');
    });
}
};
