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
        if (!Schema::hasTable('leads')){
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("lead_id")->nullable();
            $table->string("customeriid")->nullable();
            $table->date("converteddate")->nullable();
            $table->text("remarks")->nullable();
            $table->string("LeadName")->nullable();
            $table->string("category")->nullable();
            $table->text("products")->nullable();
            $table->string("Leadsource")->nullable();
            $table->string("Entrydate")->nullable();
            $table->string("Leadstatus")->nullable();
            $table->bigInteger("assigned_to")->nullable();
            $table->text("billaddress")->nullable();
            $table->text("enquirydetail")->nullable();
            $table->string("billingcountry")->nullable();
            $table->string("contactname")->nullable();
            $table->bigInteger("state_id")->nullable();
            $table->string("email",50)->nullable();
            $table->string("citylist",50)->nullable();
            $table->string("countrycode",10)->nullable();
            $table->string("phonumber",20)->nullable();
            $table->string("postalcode",10)->nullable();
            $table->string("designation")->nullable();
            $table->string("department")->nullable();
            $table->string("gst",)->nullable();
            $table->tinyInteger("proceed")->nullable();
            $table->string("quota_proceed")->nullable();
            $table->string("quota_doc")->nullable();
            $table->tinyInteger("soft_delete")->default(0);
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
