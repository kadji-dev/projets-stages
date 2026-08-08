<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Enrollment;
use App\Services\GeniusPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentPaymentController extends Controller
{
    protected GeniusPayService $geniusPay;
    protected bool $simulateMode = true; // ✅ Mode simulation activé

    public function __construct(GeniusPayService $geniusPay)
    {
        $this->geniusPay = $geniusPay;
    }

    public function index()
    {
        $user = Auth::user();
        $enrollment = $user->enrollment;

        if (!$enrollment) {
            return view('students.payments.payment', [
                'inscription' => null,
                'payments' => collect(),
                'totalPensionPaid' => 0,
                'laptopPaidAmount' => 0,
                'enrollment' => null,
                'user' => $user
            ]);
        }

        $payments = $enrollment->payments()->latest()->get();

        $totalPensionPaid = (float) $enrollment->payments()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        $inscription = (object) [
            'status' => $enrollment->hasPaidRegistrationFee() ? 'validated' : 'pending'
        ];

        $laptopPaidAmount = min($totalPensionPaid, 150000);
        $laptopEligible = $enrollment->isLaptopEligible();

        return view('students.payments.payment', compact(
            'inscription',
            'payments',
            'totalPensionPaid',
            'laptopPaidAmount',
            'enrollment',
            'user',
            'laptopEligible'
        ));
    }

    /**
     * Crée un paiement via GeniusPay (ou simulation).
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000|max:500000',
            'type' => 'required|string',
        ]);

        $user = Auth::user();
        $enrollment = $user->enrollment;

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aucun dossier d\'inscription trouvé.');
        }

        // Générer une référence unique
        $reference = 'PAY-' . date('Y') . '-' . strtoupper(Str::random(8));

        // Créer le paiement avec statut "pending"
        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $user->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_method' => 'geniuspay',
            'reference' => $reference,
            'status' => 'pending',
            'paid_at' => null,
        ]);

        // ✅ MODE SIMULATION : Valider directement le paiement
        if ($this->simulateMode) {
            return $this->simulatePayment($payment);
        }

        // ✅ MODE RÉEL : Appel à l'API GeniusPay
        try {
            $transactionData = [
                'amount' => $request->amount,
                'description' => $request->type . ' - ' . $reference,
                'email' => $user->email,
                'name' => $user->full_name,
                'phone' => $user->phone,
                'reference' => $reference,
            ];

            $transaction = $this->geniusPay->createTransaction($transactionData);

            $payment->update([
                'transaction_id' => $transaction['id'] ?? null,
            ]);

            if (isset($transaction['payment_url'])) {
                return redirect()->away($transaction['payment_url']);
            }

            return redirect()->route('payments.index')->with('error', 'Erreur lors de la redirection vers GeniusPay.');

        } catch (\Exception $e) {
            Log::error('GeniusPay Error: ' . $e->getMessage());
            return redirect()->route('payments.index')->with('error', 'Erreur de paiement: ' . $e->getMessage());
        }
    }

    /**
     * Simule un paiement (validation immédiate).
     */
    private function simulatePayment(Payment $payment)
    {
        // Valider le paiement
        $payment->status = 'completed';
        $payment->paid_at = now();
        $payment->save();

        // ✅ Gérer le paiement d'inscription (30 000 FCFA) → générer matricule
        $enrollment = $payment->enrollment;
        if ($enrollment) {
            if ($payment->isRegistrationFee() && !$enrollment->matricule) {
                $matricule = $enrollment->generateMatricule();

                // Mettre à jour l'utilisateur
                if ($enrollment->user) {
                    $enrollment->user->matricule = $matricule;
                    $enrollment->user->status = 'active';
                    $enrollment->user->save();
                }
            }

            // ✅ Mettre à jour l'éligibilité laptop
            $totalPaid = $enrollment->payments()
                ->whereIn('status', ['approved', 'completed'])
                ->sum('amount');

            if ($totalPaid >= 150000) {
                $enrollment->laptop_eligible = true;
                $enrollment->save();
            }
        }

        // Message de succès
        $message = "✅ Paiement de " . number_format($payment->amount, 0, ',', ' ') . " FCFA effectué avec succès ! Réf: " . $payment->reference;

        if ($payment->isRegistrationFee() && $enrollment && $enrollment->matricule) {
            $message .= " Votre matricule : " . $enrollment->matricule;
        }

        return redirect()->route('payments.index')->with('success', $message);
    }

    /**
     * Callback après paiement GeniusPay.
     */
    public function callback(Request $request)
    {
        // Récupérer la référence du paiement
        $reference = $request->input('reference') ?? $request->input('custom_reference');
        $transactionId = $request->input('transaction_id');

        // Trouver le paiement
        $payment = Payment::where('reference', $reference)
            ->orWhere('transaction_id', $transactionId)
            ->first();

        if (!$payment) {
            return redirect()->route('payments.index')->with('error', 'Paiement non trouvé.');
        }

        // Vérifier le statut réel du paiement via l'API
        try {
            $transaction = $this->geniusPay->getTransactionStatus($transactionId ?? $payment->transaction_id);

            if (isset($transaction['status'])) {
                if ($transaction['status'] === 'completed' || $transaction['status'] === 'approved') {
                    // Paiement réussi
                    $payment->status = 'completed';
                    $payment->paid_at = now();
                    $payment->save();

                    // Gérer l'inscription et le matricule
                    $enrollment = $payment->enrollment;
                    if ($enrollment) {
                        if ($payment->isRegistrationFee() && !$enrollment->matricule) {
                            $matricule = $enrollment->generateMatricule();
                            if ($enrollment->user) {
                                $enrollment->user->matricule = $matricule;
                                $enrollment->user->status = 'active';
                                $enrollment->user->save();
                            }
                        }

                        // Mettre à jour l'éligibilité laptop
                        $totalPaid = $enrollment->payments()
                            ->whereIn('status', ['approved', 'completed'])
                            ->sum('amount');

                        if ($totalPaid >= 150000) {
                            $enrollment->laptop_eligible = true;
                            $enrollment->save();
                        }
                    }

                    return redirect()->route('payments.index')->with('success', '✅ Paiement effectué et validé avec succès !');
                } elseif ($transaction['status'] === 'failed' || $transaction['status'] === 'cancelled') {
                    $payment->status = 'failed';
                    $payment->save();
                    return redirect()->route('payments.index')->with('error', '❌ Le paiement a échoué ou a été annulé.');
                }
            }

            return redirect()->route('payments.index')->with('info', '⏳ Paiement en cours de traitement...');

        } catch (\Exception $e) {
            Log::error('GeniusPay Callback Error: ' . $e->getMessage());
            return redirect()->route('payments.index')->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Télécharge le reçu PDF.
     */
    public function downloadReceipt(Payment $payment)
    {
        if ($payment->enrollment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$payment->isValidated()) {
            return back()->with('error', 'Ce paiement n\'est pas encore validé.');
        }

        return view('payments.receipt', compact('payment'));
    }

    /**
     * Bascule entre mode simulation et mode réel.
     */
    public function toggleSimulation(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->simulateMode = $request->input('simulate', true);
        session(['payment_simulate' => $this->simulateMode]);

        return redirect()->back()->with('success', 'Mode simulation ' . ($this->simulateMode ? 'activé' : 'désactivé'));
    }
}
