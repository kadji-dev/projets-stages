<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PreEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'departement',
        'pays',
        'sexe',
        'nationalite',
        'telephone',
        'email',
        'situation_familiale',
        'handicap',
        'bac_annee',
        'bac_serie',
        'bac_mention',
        'bac_etablissement',
        'cursus_id',
        'field_id',
        'speciality_id',
        'level_id',
        'statut_etudiant',
        'profession_chef_famille',
        'cursus_scolaire',
        'type_hebergement',
        'quartier_residence',
        'hebergement_precisions',
        'financement',
        'mode_paiement',
        'mobilite_internationale',
        'contact_urgence_nom',
        'contact_urgence_telephone',
        'contact_urgence_email',
        'fait_a',
        'date_signature',
        'academic_year_id',
        'status',
        'declaration_honneur'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_signature' => 'date',
        'cursus_scolaire' => 'array',
        'mode_paiement' => 'array',
        'declaration_honneur' => 'boolean'
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cursus(): BelongsTo
    {
        return $this->belongsTo(Cursus::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relation avec l'inscription (une pré-inscription peut devenir une inscription).
     */
    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'pre_enrollment_id');
    }

    // ============================================================
    // ATTRIBUTS PERSONNALISÉS
    // ============================================================

    /**
     * Récupère le nom complet de l'étudiant.
     */
    public function getFullNameAttribute(): string
    {
        return ($this->prenom ?? '') . ' ' . ($this->nom ?? '');
    }

    /**
     * Récupère le nom formaté pour l'affichage.
     */
    public function getDisplayNameAttribute(): string
    {
        return trim($this->full_name) ?: 'Étudiant';
    }

    // ============================================================
    // MÉTHODES MÉTIER
    // ============================================================

    /**
     * Vérifie si la pré-inscription est validée.
     */
    public function isValidated(): bool
    {
        return $this->status === 'validee';
    }

    /**
     * Vérifie si la pré-inscription est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === 'en_attente';
    }

    /**
     * Vérifie si la pré-inscription est rejetée.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejetee';
    }

    /**
     * Vérifie si la pré-inscription peut être modifiée.
     * Une pré-inscription est modifiable tant qu'elle n'est pas validée ou rejetée.
     */
    public function canBeModified(): bool
    {
        return !in_array($this->status, ['validee', 'rejetee']);
    }

    /**
     * Valide la pré-inscription.
     */
    public function validate(): void
    {
        $this->status = 'validee';
        $this->save();
    }

    /**
     * Rejette la pré-inscription.
     */
    public function reject(): void
    {
        $this->status = 'rejetee';
        $this->save();
    }

    /**
     * Vérifie si l'utilisateur est le propriétaire de la pré-inscription.
     */
    public function isOwnedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Récupère les informations de contact formatées.
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'telephone' => $this->telephone,
        ];
    }

    /**
     * Récupère les informations académiques formatées.
     */
    public function getAcademicInfoAttribute(): array
    {
        return [
            'cursus' => $this->cursus?->label ?? 'Non défini',
            'field' => $this->field?->label ?? 'Non défini',
            'speciality' => $this->speciality?->label ?? 'Tronc commun',
            'level' => $this->level?->label ?? 'Non défini',
        ];
    }

    /**
     * Récupère le statut de la pré-inscription en texte lisible.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            default => 'Inconnu',
        };
    }

    /**
     * Récupère la classe CSS du statut.
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'bg-amber-500/10 text-amber-700 border-amber-200',
            'validee' => 'bg-emerald-500/10 text-emerald-700 border-emerald-200',
            'rejetee' => 'bg-red-500/10 text-red-700 border-red-200',
            default => 'bg-zinc-100 text-zinc-700',
        };
    }
}
