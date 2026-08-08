<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->string('type'); // ex: 1ère Tranche
            $table->decimal('amount', 12, 2); // <<-- Champ manquant
            $table->string('payment_method')->default('cash'); // cash, om, momo
            $table->string('reference')->unique();
            $table->string('status')->default('pending'); // pending, approved
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
