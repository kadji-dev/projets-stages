<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajoute les colonnes nécessaires pour lier les tables entre elles.
     */
    public function up(): void
    {
        // ============================================================
        // 1. TABLE pre_enrollments
        // ============================================================
        Schema::table('pre_enrollments', function (Blueprint $table) {
            // Lier la pré-inscription à l'utilisateur
            if (!Schema::hasColumn('pre_enrollments', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Déclaration sur l'honneur
            if (!Schema::hasColumn('pre_enrollments', 'declaration_honneur')) {
                $table->boolean('declaration_honneur')
                    ->default(false)
                    ->after('date_signature');
            }
        });

        // ============================================================
        // 2. TABLE enrollments
        // ============================================================
        Schema::table('enrollments', function (Blueprint $table) {
            // Lier l'inscription à la pré-inscription
            if (!Schema::hasColumn('enrollments', 'pre_enrollment_id')) {
                $table->foreignId('pre_enrollment_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('pre_enrollments')
                    ->nullOnDelete();
            }

            // Informations de l'étudiant (copie pour faciliter l'affichage)
            if (!Schema::hasColumn('enrollments', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('enrollments', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('enrollments', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            // Programme et niveau
            if (!Schema::hasColumn('enrollments', 'program')) {
                $table->string('program')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('enrollments', 'level')) {
                $table->string('level')->nullable()->after('program');
            }
        });

        // ============================================================
        // 3. TABLE payments
        // ============================================================
        Schema::table('payments', function (Blueprint $table) {
            // Lier le paiement à l'utilisateur (pour les paiements en ligne)
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('enrollment_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Date de paiement effective
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
        });

        // ============================================================
        // 4. TABLE users
        // ============================================================
        Schema::table('users', function (Blueprint $table) {
            // Prénom et nom (pour afficher le nom complet)
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            // Téléphone
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            // Matricule (copié depuis l'inscription)
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->nullable()->unique()->after('phone');
            }

            // Statut du compte (pending, active, inactive)
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('pending')->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Supprime les colonnes ajoutées.
     */
    public function down(): void
    {
        // ============================================================
        // 1. TABLE pre_enrollments
        // ============================================================
        Schema::table('pre_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('pre_enrollments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('pre_enrollments', 'declaration_honneur')) {
                $table->dropColumn('declaration_honneur');
            }
        });

        // ============================================================
        // 2. TABLE enrollments
        // ============================================================
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'pre_enrollment_id')) {
                $table->dropForeign(['pre_enrollment_id']);
                $table->dropColumn('pre_enrollment_id');
            }
            if (Schema::hasColumn('enrollments', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('enrollments', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('enrollments', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('enrollments', 'program')) {
                $table->dropColumn('program');
            }
            if (Schema::hasColumn('enrollments', 'level')) {
                $table->dropColumn('level');
            }
        });

        // ============================================================
        // 3. TABLE payments
        // ============================================================
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('payments', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });

        // ============================================================
        // 4. TABLE users
        // ============================================================
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'matricule')) {
                $table->dropColumn('matricule');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
