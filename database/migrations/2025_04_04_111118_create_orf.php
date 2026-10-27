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
        if (!Schema::hasTable('orf')){
        Schema::create('orf', function (Blueprint $table) {
            $table->id();
            $table->string("quoproid")->nullable();
            $table->string("leano")->nullable();
            $table->string("tranref")->nullable();
            $table->string("quono")->nullable();
            $table->string("custo")->nullable();
            $table->date("quotadate")->nullable();
            $table->string("orfref")->nullable();
            $table->date("orfdate")->nullable();
            $table->string("cusporef")->nullable();
            $table->date("podate")->nullable();
            $table->string("attcuspo")->nullable();
            $table->string("sigdraw")->nullable();
            $table->date("crddate")->nullable();
            $table->text("specialins")->nullable();
            $table->string("approval_status")->nullable();
            $table->string("cs_status")->nullable();
            $table->text("reject_remark")->nullable();
            $table->text("csreject_remark")->nullable();
            $table->string("dispatchdate")->nullable();
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orf');
    }
};
