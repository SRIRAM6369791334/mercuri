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
         Schema::create('mailsettings', function (Blueprint $table) {
        $table->id();
        $table->string('from_email');
        $table->text('cc_email')->nullable(); // multiple CC emails (comma separated)
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailsettings');
    }
};
