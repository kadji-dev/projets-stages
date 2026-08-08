<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matricule',
        'status',
        'degree_type',
        'specialty',
        'laptop_eligible',
        'pre_enrollment_id',
        'name',
        'email',
        'phone',
        'program',
        'level'
    ];

    protected $casts = [
        'laptop_eligible' => 'boolean',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function preEnrollment(): BelongsTo
    {
        return $this->belongsTo(PreEnrollment::class);
    }

    // ============================================================
    // ATTRIBUTS PERSONNALISÉS
    // ============================================================

    public function getFullNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->full_name;
        }
        return $this->name ?? 'Étudiant';
    }

    public function getFirstNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->first_name;
        }
        $nameParts = explode(' ', $this->name ?? '');
        return $nameParts[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->last_name;
        }
        $nameParts = explode(' ', $this->name ?? '');
        return end($nameParts) ?? '';
    }

    /**
     * Récupère le statut en texte lisible.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'enrolled' => 'Inscrit',
            'in_progress' => 'En cours',
            'validated' => 'Validé',
            default => 'Inconnu',
        };
    }

    /**
     * Récupère la classe CSS du statut.
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-amber-500/10 text-amber-700 border-amber-200',
            'enrolled' => 'bg-emerald-500/10 text-emerald-700 border-emerald-200',
            'in_progress' => 'bg-blue-500/10 text-blue-700 border-blue-200',
            'validated' => 'bg-emerald-500/10 text-emerald-700 border-emerald-200',
            default => 'bg-zinc-100 text-zinc-700',
        };
    }

    /**
     * Récupère le montant restant à payer pour la pension (400 000 FCFA).
     */
    public function getRemainingTuitionAttribute(): float
    {
        return max(0, 400000 - $this->totalPaidTuition());
    }

    /**
     * Récupère le montant restant pour le laptop (150 000 FCFA).
     */
    public function getRemainingLaptopAmountAttribute(): float
    {
        return max(0, 150000 - $this->laptopProgressAmount());
    }

    // ============================================================
    // MÉTHODES MÉTIER
    // ============================================================

    /**
     * Vérifie si les frais d'inscription (30 000 FCFA) ont été payés.
     */
    public function hasPaidRegistrationFee(): bool
    {
        return $this->payments()
            ->where('type', 'inscription')
            ->whereIn('status', ['approved', 'completed'])
            ->exists();
    }

    /**
     * Calcule le montant total payé (tous paiements validés confondus).
     */
    public function totalPaidTuition(): float
    {
        return (float) $this->payments()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');
    }

    /**
     * Calcule la progression pour le laptop (plafonné à 150 000 FCFA).
     */
    public function laptopProgressAmount(): float
    {
        return min($this->totalPaidTuition(), 150000);
    }

    /**
     * Vérifie si l'étudiant est éligible au laptop.
     * Seuil : 150 000 FCFA atteint.
     */
    public function isLaptopEligible(): bool
    {
        return $this->laptop_eligible || $this->totalPaidTuition() >= 150000;
    }

    /**
     * Vérifie si l'étudiant est inscrit (matricule attribué).
     */
    public function isEnrolled(): bool
    {
        return $this->status === 'enrolled' && !empty($this->matricule);
    }

    /**
     * Vérifie si l'étudiant a commencé le paiement de la pension.
     */
    public function hasStartedTuition(): bool
    {
        return $this->totalPaidTuition() > 0;
    }

    /**
     * Met à jour l'éligibilité laptop automatiquement.
     */
    public function updateLaptopEligibility(): bool
    {
        $this->laptop_eligible = $this->totalPaidTuition() >= 150000;
        $this->save();
        return $this->laptop_eligible;
    }

    /**
     * Génère un matricule automatique.
     */
    public function generateMatricule(): string
    {
        if ($this->matricule) {
            return $this->matricule;
        }

        $nextId = Enrollment::max('id') + 1;
        $matricule = 'ESC-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $this->matricule = $matricule;
        $this->save();

        return $matricule;
    }

    /**
     * Valide l'inscription (attribue le matricule et passe le statut à enrolled).
     */
    public function validateEnrollment(): void
    {
        if (!$this->matricule) {
            $this->generateMatricule();
        }

        $this->status = 'enrolled';
        $this->save();

        // Mettre à jour l'utilisateur associé
        if ($this->user) {
            $this->user->matricule = $this->matricule;
            $this->user->status = 'active';
            $this->user->save();
        }

        // Mettre à jour la pré-inscription si elle existe
        if ($this->preEnrollment) {
            $this->preEnrollment->status = 'validee';
            $this->preEnrollment->save();
        }
    }

    /**
     * Crée une inscription à partir d'une pré-inscription validée.
     */
    public static function createFromPreEnrollment(PreEnrollment $preEnrollment): self
    {
        // Vérifier si une inscription existe déjà
        $existing = self::where('user_id', $preEnrollment->user_id)->first();
        if ($existing) {
            return $existing;
        }

        $enrollment = self::create([
            'user_id' => $preEnrollment->user_id,
            'pre_enrollment_id' => $preEnrollment->id,
            'name' => $preEnrollment->nom . ' ' . $preEnrollment->prenom,
            'email' => $preEnrollment->email,
            'phone' => $preEnrollment->telephone,
            'degree_type' => $preEnrollment->level?->label ?? 'Bac+2',
            'specialty' => $preEnrollment->speciality?->label ?? 'Tronc commun',
            'status' => 'pending',
            'laptop_eligible' => false,
            'program' => $preEnrollment->field?->label ?? '',
            'level' => $preEnrollment->level?->label ?? '',
        ]);

        return $enrollment;
    }

    /**
     * Vérifie si le paiement de l'inscription (30 000 FCFA) a été effectué
     * et génère le matricule si nécessaire.
     */
    public function handleRegistrationPayment(): void
    {
        if ($this->hasPaidRegistrationFee() && !$this->matricule) {
            $this->generateMatricule();

            if ($this->user) {
                $this->user->matricule = $this->matricule;
                $this->user->status = 'active';
                $this->user->save();
            }
        }
    }

    /**
     * Récupère le pourcentage de progression de la pension.
     */
    public function getPensionProgressAttribute(): float
    {
        return min(100, round(($this->totalPaidTuition() / 400000) * 100));
    }

    /**
     * Récupère le pourcentage de progression du laptop.
     */
    public function getLaptopProgressAttribute(): float
    {
        return min(100, round(($this->laptopProgressAmount() / 150000) * 100));
    }
}
