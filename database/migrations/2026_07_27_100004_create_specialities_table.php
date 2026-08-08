<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->string('code', 20); // GL, RSI
            $table->string('label'); // Génie Logiciel
            $table->timestamps();

            $table->unique(['field_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialities');
    }
};
