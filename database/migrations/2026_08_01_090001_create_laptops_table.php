<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique(); // Ex: PC-0231
            $table->string('brand', 50);                // Dell, HP, Lenovo...
            $table->string('model', 100);                // Latitude 5480...
            $table->string('serial_number', 100)->nullable()->unique();
            $table->enum('status', ['disponible', 'attribue', 'maintenance'])->default('disponible');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laptops');
    }
};
