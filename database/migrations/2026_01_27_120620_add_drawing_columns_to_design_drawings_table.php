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
        Schema::table('design_drawings', function (Blueprint $table) {
            $table->string('drawing_app')->nullable()->after('file_path');
            $table->text('drawing_remarks')->nullable()->after('drawing_app');
        });
    }

    public function down(): void
    {
        Schema::table('design_drawings', function (Blueprint $table) {
            $table->dropColumn(['drawing_app', 'drawing_remarks']);
        });
    }
};
