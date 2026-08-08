<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->foreignId('speciality_id')->nullable()->constrained('specialities')->nullOnDelete();
            $table->string('code', 10); // L1, L2, L3, BTS1, BTS2...
            $table->string('label'); // Niveau 1, BTS 1ère Année...
            $table->unsignedTinyInteger('order')->default(1);
            $table->timestamps();

            $table->unique(['field_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
