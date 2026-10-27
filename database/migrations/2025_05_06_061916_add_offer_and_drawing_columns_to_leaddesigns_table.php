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
        Schema::table('leaddesigns', function (Blueprint $table) {
            $table->string('offer')->nullable();
            $table->string('drawing')->nullable();
            $table->string('pricevalue')->nullable();
            $table->string('basicsupply')->nullable();
            $table->string('freightvalue')->nullable();
            $table->string('installvalue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaddesigns', function (Blueprint $table) {
            $table->dropColumn([
                'offer',
                'drawing',
                'pricevalue',
                'basicsupply',
                'freightvalue',
                'installvalue'
            ]);
        });
    }
};
