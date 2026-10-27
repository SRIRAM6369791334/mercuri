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
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasIndex('leads', 'leads_phonumber_index')) {
                $table->index('phonumber', 'leads_phonumber_index');
            }
            if (!Schema::hasIndex('leads', 'leads_leadsource_index')) {
                $table->index('Leadsource', 'leads_leadsource_index');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasIndex('customers', 'customers_contact_no_index')) {
                $table->index('contact_no', 'customers_contact_no_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_phonumber_index');
            $table->dropIndex('leads_leadsource_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_contact_no_index');
        });
    }
};
