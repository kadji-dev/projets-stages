<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StaffPaymentController extends Controller
{
    public function index(Request $request)
    {
        // ✅ CORRIGÉ : Seuil à 150 000 FCFA (et non 250 000)
        $tranche1Threshold = 150000;

        // KPIs Financiers Dynamiques
        $totalEncaisse = Payment::whereIn('status', ['approved', 'completed'])->sum('amount');
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');

        $totalEnrolled = Enrollment::where('status', 'enrolled')->count();
        $upToDateCount = Enrollment::where('status', 'enrolled')->get()->filter(function ($student) use ($tranche1Threshold) {
            return $student->payments->whereIn('status', ['approved', 'completed'])->sum('amount') >= $tranche1Threshold;
        })->count();

        // Filtrage des paiements avec relations
        $query = Payment::with('enrollment.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('enrollment', function($e) use ($search) {
                      $e->where('matricule', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        // Récupérer les étudiants pour le formulaire de paiement cash
        $students = Enrollment::with('user')->latest()->get();

        return view('payments.payment', compact(
            'totalEncaisse',
            'pendingAmount',
            'upToDateCount',
            'totalEnrolled',
            'payments',
            'students'
        ));
    }

    /**
     * Enregistre un paiement cash (guichet).
     */
    public function storeCash(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'type'          => 'required|string',
            'amount'        => 'required|numeric|min:1000',
            'reference'     => 'nullable|string|unique:payments,reference',
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);

        // Générer une référence si non fournie
        $reference = $validated['reference'] ?? 'REC-CASH-' . strtoupper(Str::random(8));

        $payment = Payment::create([
            'enrollment_id' => $validated['enrollment_id'],
            'user_id' => $enrollment->user_id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'payment_method' => 'cash',
            'reference' => $reference,
            'status' => 'approved',
            'paid_at' => now(),
        ]);

        // Mise à jour automatique de l'éligibilité laptop
        $this->updateLaptopEligibility($payment->enrollment_id);

        return redirect()->back()->with('success', "Paiement cash enregistré. Réf: {$reference}");
    }

    /**
     * Approuve un paiement en attente.
     */
    public function approve(Payment $payment)
    {
        $payment->status = 'approved';
        $payment->paid_at = now();
        $payment->save();

        $this->updateLaptopEligibility($payment->enrollment_id);

        return redirect()->back()->with('success', 'Paiement confirmé.');
    }

    /**
     * Met à jour l'éligibilité laptop d'un étudiant.
     * ✅ CORRIGÉ : Seuil à 150 000 FCFA
     */
    private function updateLaptopEligibility($enrollmentId)
    {
        $student = Enrollment::find($enrollmentId);
        if ($student) {
            $totalPaid = $student->payments()
                ->whereIn('status', ['approved', 'completed'])
                ->sum('amount');

            // ✅ Seuil corrigé : 150 000 FCFA
            $student->laptop_eligible = $totalPaid >= 150000;
            $student->save();
        }
    }

    /**
     * Génère un reçu PDF pour un paiement.
     */
    public function downloadReceipt(Payment $payment)
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // Logique de génération du reçu PDF
        return view('payments.receipt', compact('payment'));
    }

    /**
     * Supprime un paiement (optionnel).
     */
    public function destroy(Payment $payment)
    {
        $enrollmentId = $payment->enrollment_id;
        $payment->delete();

        // Mettre à jour l'éligibilité après suppression
        $this->updateLaptopEligibility($enrollmentId);

        return redirect()->back()->with('success', 'Paiement supprimé.');
    }
}
