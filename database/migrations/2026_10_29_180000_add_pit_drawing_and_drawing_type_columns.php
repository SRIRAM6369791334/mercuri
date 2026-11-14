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
        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'pit_drawing')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('pit_drawing')->nullable()->after('ganumber');
            });
        }

        if (Schema::hasTable('quotations') && !Schema::hasColumn('quotations', 'pit_drawing')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->string('pit_drawing')->nullable()->after('ga_number');
            });
        }

        if (Schema::hasTable('designs') && !Schema::hasColumn('designs', 'pit_drawing')) {
            Schema::table('designs', function (Blueprint $table) {
                $table->string('pit_drawing')->nullable()->after('ganumber');
            });
        }

        if (Schema::hasTable('design_drawings') && !Schema::hasColumn('design_drawings', 'drawing_type')) {
            Schema::table('design_drawings', function (Blueprint $table) {
                $table->string('drawing_type')->nullable()->default('ga')->after('file_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'pit_drawing')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('pit_drawing');
            });
        }

        if (Schema::hasTable('quotations') && Schema::hasColumn('quotations', 'pit_drawing')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropColumn('pit_drawing');
            });
        }

        if (Schema::hasTable('designs') && Schema::hasColumn('designs', 'pit_drawing')) {
            Schema::table('designs', function (Blueprint $table) {
                $table->dropColumn('pit_drawing');
            });
        }

        if (Schema::hasTable('design_drawings') && Schema::hasColumn('design_drawings', 'drawing_type')) {
            Schema::table('design_drawings', function (Blueprint $table) {
                $table->dropColumn('drawing_type');
            });
        }
    }
};
