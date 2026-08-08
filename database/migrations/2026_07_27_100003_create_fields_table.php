<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cursus_id')->constrained('cursuses')->cascadeOnDelete();
            $table->string('code', 20); // GI, GES, GC
            $table->string('label'); // Génie Informatique
            $table->timestamps();

            $table->unique(['cursus_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
