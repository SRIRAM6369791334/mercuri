<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Biometric for Forklift"
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('tax_percent', 5, 2)->default(18.00); // GST %
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('accessories');
    }
};