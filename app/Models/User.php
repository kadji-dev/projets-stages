<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'first_name', 'last_name', 'phone', 'matricule', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    /**
     * Relation avec la pré-inscription de l'utilisateur.
     */
    public function preEnrollment(): HasOne
    {
        return $this->hasOne(PreEnrollment::class);
    }

    /**
     * Relation avec l'inscription de l'utilisateur.
     */
    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class);
    }

    /**
     * Relation avec les paiements effectués par l'utilisateur.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ============================================================
    // ATTRIBUTS PERSONNALISÉS
    // ============================================================

    /**
     * Récupère le nom complet de l'utilisateur.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name ?? 'Utilisateur';
    }

    /**
     * Récupère le prénom (fallback sur le nom si non défini).
     */
    public function getFirstNameAttribute(): string
    {
        return $this->attributes['first_name'] ?? explode(' ', $this->name ?? '')[0] ?? '';
    }

    /**
     * Récupère le nom de famille (fallback).
     */
    public function getLastNameAttribute(): string
    {
        $nameParts = explode(' ', $this->name ?? '');
        return end($nameParts) ?? '';
    }

    // ============================================================
    // MÉTHODES UTILES
    // ============================================================

    /**
     * Vérifie si l'utilisateur est un administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'staff';
    }

    /**
     * Vérifie si l'utilisateur est un étudiant.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Vérifie si l'utilisateur a un matricule (est inscrit).
     */
    public function hasMatricule(): bool
    {
        return !empty($this->matricule);
    }

    /**
     * Vérifie si l'utilisateur a payé les frais d'inscription.
     */
    public function hasPaidRegistrationFee(): bool
    {
        return $this->enrollment?->hasPaidRegistrationFee() ?? false;
    }

    /**
     * Récupère le total payé par l'utilisateur.
     */
    public function totalPaid(): float
    {
        return $this->enrollment?->totalPaidTuition() ?? 0.0;
    }

    /**
     * Récupère le montant payé pour le laptop.
     */
    public function laptopPaidAmount(): float
    {
        return $this->enrollment?->laptopProgressAmount() ?? 0.0;
    }

    /**
     * Vérifie si l'utilisateur est éligible au laptop.
     */
    public function isLaptopEligible(): bool
    {
        return $this->enrollment?->isLaptopEligible() ?? false;
    }

    /**
     * Vérifie si l'utilisateur a une pré-inscription.
     */
    public function hasPreEnrollment(): bool
    {
        return $this->preEnrollment !== null;
    }

    /**
     * Vérifie si l'utilisateur a une pré-inscription validée.
     */
    public function hasValidatedPreEnrollment(): bool
    {
        return $this->preEnrollment?->isValidated() ?? false;
    }

    /**
     * Vérifie si l'utilisateur a une inscription.
     */
    public function hasEnrollment(): bool
    {
        return $this->enrollment !== null;
    }

    /**
     * Vérifie si l'utilisateur est inscrit (matricule attribué).
     */
    public function isEnrolled(): bool
    {
        return $this->enrollment?->isEnrolled() ?? false;
    }

    /**
     * Vérifie si l'utilisateur peut modifier sa pré-inscription.
     */
    public function canModifyPreEnrollment(): bool
    {
        return $this->preEnrollment?->canBeModified() ?? true;
    }

    /**
     * Récupère le statut du parcours de l'utilisateur.
     */
    public function getParcoursStatusAttribute(): string
    {
        if ($this->isEnrolled()) {
            return 'inscrit';
        }
        if ($this->hasValidatedPreEnrollment()) {
            return 'pre_inscription_validee';
        }
        if ($this->hasPreEnrollment()) {
            return 'pre_inscription_en_attente';
        }
        return 'nouveau';
    }
}
