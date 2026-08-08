<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\PreEnrollment;
use Illuminate\Support\Facades\Auth;

class StudentEnrollmentController extends Controller
{
    /**
     * Affiche l'état de l'inscription et le matricule de l'étudiant.
     */
    public function index()
    {
        $user = Auth::user();

        // Récupération de l'inscription liée à l'utilisateur connecté
        $enrollment = Enrollment::where('user_id', $user->id)->first();

        // Récupérer la pré-inscription de l'utilisateur
        $preEnrollment = $user->preEnrollment;

        // Si l'utilisateur a une pré-inscription validée mais pas d'inscription
        if (!$enrollment && $preEnrollment && $preEnrollment->isValidated()) {
            // Créer automatiquement une inscription à partir de la pré-inscription
            $enrollment = Enrollment::createFromPreEnrollment($preEnrollment);
        }

        // Vérifier si l'utilisateur a une pré-inscription en attente
        $hasPendingPreEnrollment = $preEnrollment && $preEnrollment->isPending();

        // Vérifier si l'utilisateur a déjà une inscription
        $hasEnrollment = $enrollment !== null;

        // Vérifier si l'inscription est validée (matricule attribué)
        $isEnrolled = $enrollment && $enrollment->isEnrolled();

        // Vérifier si l'utilisateur a payé les frais d'inscription
        $hasPaidRegistrationFee = $enrollment && $enrollment->hasPaidRegistrationFee();

        // Récupérer le statut de l'inscription
        $status = $enrollment ? $enrollment->status : 'none';

        // Récupérer le matricule
        $matricule = $enrollment ? $enrollment->matricule : null;

        return view('students.enrollments.enrollment', compact(
            'enrollment',
            'preEnrollment',
            'hasPendingPreEnrollment',
            'hasEnrollment',
            'isEnrolled',
            'status',
            'hasPaidRegistrationFee',
            'matricule',
            'user'
        ));
    }

    /**
     * Vérifie si l'étudiant peut accéder à la page d'inscription.
     * Retourne les informations nécessaires à l'affichage.
     */
    public function status()
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->first();
        $preEnrollment = $user->preEnrollment;

        $data = [
            'has_pre_enrollment' => $preEnrollment !== null,
            'pre_enrollment_status' => $preEnrollment ? $preEnrollment->status : null,
            'pre_enrollment_can_modify' => $preEnrollment ? $preEnrollment->canBeModified() : true,
            'has_enrollment' => $enrollment !== null,
            'enrollment_status' => $enrollment ? $enrollment->status : null,
            'matricule' => $enrollment ? $enrollment->matricule : null,
            'is_enrolled' => $enrollment && $enrollment->isEnrolled(),
            'has_paid_registration_fee' => $enrollment && $enrollment->hasPaidRegistrationFee(),
            'can_proceed' => $enrollment && $enrollment->status === 'pending',
            'can_pay_registration' => $enrollment && !$enrollment->hasPaidRegistrationFee(),
            'laptop_eligible' => $enrollment && $enrollment->isLaptopEligible(),
            'total_paid' => $enrollment ? $enrollment->totalPaidTuition() : 0,
            'pension_progress' => $enrollment ? $enrollment->pension_progress : 0,
        ];

        return response()->json($data);
    }

    /**
     * Demande la validation de l'inscription par le staff.
     * (Optionnel - peut être utilisé pour notifier le staff)
     */
    public function requestValidation()
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aucune inscription trouvée.');
        }

        if ($enrollment->isEnrolled()) {
            return redirect()->back()->with('info', 'Votre inscription est déjà validée.');
        }

        if ($enrollment->status === 'pending') {
            // Vérifier si les frais d'inscription sont payés
            if (!$enrollment->hasPaidRegistrationFee()) {
                return redirect()->back()->with('error', 'Veuillez d\'abord payer les frais d\'inscription (30 000 FCFA) pour valider votre inscription.');
            }

            // ✅ Si les frais sont payés, générer automatiquement le matricule
            if (!$enrollment->matricule) {
                $matricule = $enrollment->generateMatricule();

                // Mettre à jour l'utilisateur
                if ($enrollment->user) {
                    $enrollment->user->matricule = $matricule;
                    $enrollment->user->status = 'active';
                    $enrollment->user->save();
                }
            }

            // Mettre à jour le statut
            $enrollment->status = 'enrolled';
            $enrollment->save();

            return redirect()->back()->with('success', '✅ Félicitations ! Votre inscription est validée. Matricule : ' . $enrollment->matricule);
        }

        return redirect()->back()->with('error', 'Action non autorisée.');
    }

    /**
     * Affiche la carte d'étudiant (matricule, photo, etc.)
     */
    public function card()
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->first();

        if (!$enrollment || !$enrollment->isEnrolled()) {
            return redirect()->route('enrollments.index')
                ->with('error', 'Vous devez être inscrit pour accéder à votre carte d\'étudiant.');
        }

        return view('students.enrollments.card', compact('enrollment', 'user'));
    }

    /**
     * Récupère les informations de paiement pour le dashboard.
     */
    public function paymentInfo()
    {
        $user = Auth::user();
        $enrollment = Enrollment::where('user_id', $user->id)->first();

        if (!$enrollment) {
            return response()->json([
                'has_enrollment' => false,
                'message' => 'Aucune inscription trouvée.'
            ]);
        }

        $data = [
            'has_enrollment' => true,
            'matricule' => $enrollment->matricule,
            'is_enrolled' => $enrollment->isEnrolled(),
            'has_paid_registration' => $enrollment->hasPaidRegistrationFee(),
            'total_paid' => $enrollment->totalPaidTuition(),
            'pension_progress' => $enrollment->pension_progress,
            'laptop_progress' => $enrollment->laptop_progress,
            'laptop_eligible' => $enrollment->isLaptopEligible(),
            'remaining_tuition' => $enrollment->remaining_tuition,
            'remaining_laptop' => $enrollment->remaining_laptop_amount,
            'payments' => $enrollment->payments()->latest()->limit(5)->get()->map(function ($payment) {
                return [
                    'reference' => $payment->reference,
                    'type' => $payment->type_label,
                    'amount' => $payment->formatted_amount,
                    'status' => $payment->status_label,
                    'date' => $payment->formatted_date,
                ];
            }),
        ];

        return response()->json($data);
    }

    /**
     * Génère le matricule manuellement (admin).
     */
    public function generateMatricule($enrollmentId)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $enrollment = Enrollment::findOrFail($enrollmentId);

        if ($enrollment->matricule) {
            return redirect()->back()->with('info', 'Matricule déjà existant : ' . $enrollment->matricule);
        }

        $matricule = $enrollment->generateMatricule();

        if ($enrollment->user) {
            $enrollment->user->matricule = $matricule;
            $enrollment->user->status = 'active';
            $enrollment->user->save();
        }

        return redirect()->back()->with('success', 'Matricule généré : ' . $matricule);
    }
}
