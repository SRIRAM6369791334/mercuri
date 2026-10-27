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
        if (!Schema::hasTable('leaddesigns')){
        Schema::create('leaddesigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("leadiid")->nullable();
            $table->string("catename")->nullable();
            $table->string("proname")->nullable();
            $table->longText("design")->nullable();
            $table->string("document")->nullable();
            $table->string("assignee")->nullable();
            $table->string("assigneeto")->nullable();
            $table->string("reverse")->nullable();
            $table->tinyInteger("softde")->default(0);
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaddesigns');
    }
};
