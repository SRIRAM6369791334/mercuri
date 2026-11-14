<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orf', 'check_commercial')) {
            Schema::table('orf', function (Blueprint $table) {
                $table->boolean('check_commercial')->default(0)->after('check_drawing');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orf', 'check_commercial')) {
            Schema::table('orf', function (Blueprint $table) {
                $table->dropColumn('check_commercial');
            });
        }
    }
};
