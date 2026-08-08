<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'type',
        'amount',
        'payment_method',
        'reference',
        'status',
        'user_id',
        'paid_at',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Relation avec l'utilisateur (pour les paiements en ligne via GeniusPay).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================================
    // ATTRIBUTS PERSONNALISÉS
    // ============================================================

    /**
     * Récupère le libellé du type de paiement.
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'inscription' => 'Frais d\'inscription',
            'pension_tranche1' => 'Pension (1ère Tranche / PC)',
            'pension_solde' => 'Pension (Scolarité)',
            '1ère Tranche Scolarité' => '1ère Tranche Scolarité',
            '2ème Tranche Scolarité' => '2ème Tranche Scolarité',
            'Frais de pré-inscription' => 'Frais de pré-inscription',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Récupère le libellé du statut.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'En attente',
            'approved' => 'Validé',
            'completed' => 'Complété',
            'failed' => 'Échoué',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Récupère la classe CSS du statut pour l'affichage.
     */
    public function getStatusClassAttribute(): string
    {
        $classes = [
            'pending' => 'bg-amber-500/10 text-amber-700 border border-amber-200',
            'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'completed' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'failed' => 'bg-red-50 text-red-700 border border-red-200',
        ];
        return $classes[$this->status] ?? 'bg-zinc-100 text-zinc-700';
    }

    /**
     * Récupère le badge HTML du statut.
     */
    public function getStatusBadgeAttribute(): string
    {
        return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ' . $this->status_class . '">
            <span class="w-1.5 h-1.5 rounded-full ' . ($this->isValidated() ? 'bg-emerald-500' : 'bg-amber-500') . '"></span>
            ' . $this->status_label . '
        </span>';
    }

    /**
     * Récupère le libellé du mode de paiement.
     */
    public function getMethodLabelAttribute(): string
    {
        $labels = [
            'cash' => 'Espèces (Guichet)',
            'geniuspay' => 'GeniusPay (En ligne)',
            'om' => 'Orange Money',
            'momo' => 'MTN Mobile Money',
        ];
        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    // ============================================================
    // MÉTHODES MÉTIER
    // ============================================================

    /**
     * Vérifie si le paiement est validé.
     */
    public function isValidated(): bool
    {
        return in_array($this->status, ['approved', 'completed']);
    }

    /**
     * Vérifie si le paiement est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifie si le paiement est un paiement en ligne (GeniusPay).
     */
    public function isOnline(): bool
    {
        return $this->payment_method === 'geniuspay';
    }

    /**
     * Vérifie si le paiement est un paiement cash (guichet).
     */
    public function isCash(): bool
    {
        return $this->payment_method === 'cash';
    }

    /**
     * Vérifie si le paiement est le paiement d'inscription (30 000 FCFA).
     */
    public function isRegistrationFee(): bool
    {
        return $this->type === 'inscription';
    }

    /**
     * Vérifie si le paiement est une tranche de pension.
     */
    public function isTuitionPayment(): bool
    {
        return in_array($this->type, ['pension_tranche1', 'pension_solde', '1ère Tranche Scolarité', '2ème Tranche Scolarité']);
    }

    /**
     * Valide le paiement.
     */
    public function validate(): void
    {
        $this->status = 'approved';
        $this->paid_at = now();
        $this->save();
    }

    /**
     * Marque le paiement comme échoué.
     */
    public function fail(): void
    {
        $this->status = 'failed';
        $this->save();
    }

    /**
     * Récupère l'étudiant associé au paiement.
     */
    public function getStudentAttribute()
    {
        return $this->enrollment?->user;
    }

    /**
     * Récupère le nom de l'étudiant.
     */
    public function getStudentNameAttribute(): string
    {
        return $this->enrollment?->full_name ?? 'Étudiant inconnu';
    }

    /**
     * Récupère le matricule de l'étudiant.
     */
    public function getStudentMatriculeAttribute(): string
    {
        return $this->enrollment?->matricule ?? 'Non attribué';
    }

    /**
     * Formate le montant en FCFA.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Récupère la date formatée.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->paid_at?->format('d/m/Y à H:i') ?? $this->created_at?->format('d/m/Y H:i') ?? '—';
    }

    /**
     * Récupère la référence formatée.
     */
    public function getFormattedReferenceAttribute(): string
    {
        return $this->reference ?? '—';
    }

    /**
     * Récupère le montant en format court.
     */
    public function getAmountShortAttribute(): string
    {
        if ($this->amount >= 1000000) {
            return number_format($this->amount / 1000000, 1) . 'M';
        }
        if ($this->amount >= 1000) {
            return number_format($this->amount / 1000, 0) . 'K';
        }
        return (string) $this->amount;
    }

    /**
     * Vérifie si le paiement peut être annulé.
     */
    public function isCancellable(): bool
    {
        return $this->isPending() && !$this->isValidated();
    }

    /**
     * Annule le paiement.
     */
    public function cancel(): void
    {
        if ($this->isCancellable()) {
            $this->status = 'cancelled';
            $this->save();
        }
    }
}
