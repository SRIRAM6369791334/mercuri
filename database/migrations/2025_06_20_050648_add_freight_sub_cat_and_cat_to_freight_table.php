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
            $table->unsignedBigInteger('freight_sub_category_id')->nullable()->after('freight_detail_id');
            $table->unsignedBigInteger('freight_category_id')->nullable()->after('freight_sub_category_id');

            // Optionally, add foreign key constraints
            // $table->foreign('freight_sub_category_id')->references('id')->on('freight_sub_category')->onDelete('set null');
            // $table->foreign('freight_category_id')->references('id')->on('freight_category')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('freight', function (Blueprint $table) {
            $table->dropColumn(['freight_sub_category_id', 'freight_category_id']);
        });
    }
};
