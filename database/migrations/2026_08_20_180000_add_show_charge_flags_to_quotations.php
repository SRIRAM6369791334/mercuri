<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('show_installation_charge')->default(true)->after('installation_charge');
            $table->boolean('show_freight_charge')->default(true)->after('freight_charge');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['show_installation_charge', 'show_freight_charge']);
        });
    }
};
