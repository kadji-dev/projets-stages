<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // BTS, HND, LICENCE, MASTER
            $table->string('label'); // Brevet de Technicien Supérieur
            $table->unsignedTinyInteger('duration_years'); // 2, 3, 5...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursuses');
    }
};
