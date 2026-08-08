<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();

            // Étape 1 — Informations personnelles
            $table->string('photo')->nullable();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('lieu_naissance')->nullable();
            $table->string('departement')->nullable();
            $table->string('pays')->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->string('nationalite')->nullable();
            $table->enum('situation_familiale', ['marie', 'celibataire'])->nullable();
            $table->enum('handicap', ['oui', 'non'])->nullable();

            // Étape 2 — Parcours académique
            $table->string('bac_annee')->nullable();
            $table->string('bac_serie')->nullable();
            $table->string('bac_mention')->nullable();
            $table->string('bac_etablissement')->nullable();
            $table->foreignId('cursus_id')->constrained('cursuses');
            $table->foreignId('field_id')->constrained('fields');
            $table->foreignId('speciality_id')->nullable()->constrained('specialities')->nullOnDelete();
            $table->foreignId('level_id')->constrained('levels');
            $table->string('statut_etudiant')->nullable();
            $table->string('profession_chef_famille')->nullable();
            $table->json('cursus_scolaire')->nullable();

            // Étape 3 — Logistique & financement
            $table->string('type_hebergement')->nullable();
            $table->string('quartier_residence')->nullable();
            $table->text('hebergement_precisions')->nullable();
            $table->string('financement')->nullable();
            $table->json('mode_paiement')->nullable();
            $table->enum('mobilite_internationale', ['oui', 'non'])->nullable();
            $table->string('contact_urgence_nom')->nullable();
            $table->string('contact_urgence_telephone')->nullable();
            $table->string('contact_urgence_email')->nullable();
            $table->string('signature')->nullable();
            $table->string('fait_a')->nullable();
            $table->date('date_signature')->nullable();

            // Rattachement + suivi admin
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->enum('status', ['en_attente', 'validee', 'rejetee'])->default('en_attente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_enrollments');
    }
};
