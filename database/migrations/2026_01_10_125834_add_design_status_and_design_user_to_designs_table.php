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
        Schema::table('designs', function (Blueprint $table) {

            $table->string('design_status')
                  ->nullable()
                  ->after('assigndesign'); // adjust column position if needed

            $table->unsignedBigInteger('design_user')
                  ->nullable()
                  ->after('design_status');

            // Optional FK (recommended if design_user = users.id)
            $table->foreign('design_user')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {

            $table->dropForeign(['design_user']);
            $table->dropColumn(['design_status', 'design_user']);
        });
    }
};
