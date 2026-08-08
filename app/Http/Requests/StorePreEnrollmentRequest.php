<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Étape 1
            'photo' => 'nullable|image|max:2048',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date|before:-15 years',
            'lieu_naissance' => 'nullable|string|max:100',
            'departement' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'sexe' => 'required|in:M,F',
            'nationalite' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'situation_familiale' => 'nullable|in:marie,celibataire',
            'handicap' => 'nullable|in:oui,non',

            // Étape 2
            'bac_annee' => 'nullable|string|max:4',
            'bac_serie' => 'nullable|string|max:20',
            'bac_mention' => 'nullable|string|max:30',
            'bac_etablissement' => 'nullable|string|max:150',
            'cursus_id' => 'required|exists:cursuses,id',
            'field_id' => 'required|exists:fields,id',
            'speciality_id' => 'nullable|exists:specialities,id',
            'level_id' => 'required|exists:levels,id',
            'statut_etudiant' => 'nullable|string|max:100',
            'profession_chef_famille' => 'nullable|string|max:100',
            'cursus_2024_2025' => 'nullable|string|max:150',
            'cursus_2023_2024' => 'nullable|string|max:150',
            'cursus_2022_2023' => 'nullable|string|max:150',
            'cursus_2021_2022' => 'nullable|string|max:150',

            // Étape 3
            'type_hebergement' => 'nullable|string|max:50',
            'quartier_residence' => 'nullable|string|max:150',
            'hebergement_precisions' => 'nullable|string|max:1000',
            'financement' => 'nullable|in:personnel,employeur,bourse,autre',
            'paiement' => 'nullable|array',
            'paiement.*' => 'in:especes,mandat,cheque,virement',
            'mobilite_internationale' => 'nullable|in:oui,non',
            'contact_urgence_nom' => 'nullable|string|max:150',
            'contact_urgence_telephone' => 'nullable|string|max:30',
            'contact_urgence_email' => 'nullable|email|max:150',
            'fait_a' => 'nullable|string|max:100',
            'date_signature' => 'nullable|date',
            'declaration_honneur' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'declaration_honneur.required' => 'Vous devez cocher la déclaration sur l\'honneur.',
            'declaration_honneur.accepted' => 'Vous devez cocher la déclaration sur l\'honneur.',
            'date_naissance.before' => 'Le candidat doit avoir au moins 15 ans.',
            'bac_annee.max' => 'L\'année d\'obtention doit être au format AAAA (ex: 2024).',
        ];
    }
}
