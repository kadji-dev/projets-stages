<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePreEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\Cursus;
use App\Models\PreEnrollment;
use App\Models\Enrollment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PreEnrollmentController extends Controller
{
    /**
     * Affiche le formulaire de pré-inscription.
     */
    public function index()
    {
        $cursuses = Cursus::with(['fields.specialities', 'fields.levels'])
            ->orderBy('label')
            ->get();

        // Récupérer la pré-inscription existante de l'utilisateur
        $preEnrollment = Auth::user()->preEnrollment;

        // Vérifier si l'utilisateur peut modifier la pré-inscription
        $canModify = true;
        if ($preEnrollment) {
            $canModify = $preEnrollment->canBeModified();
        }

        return view('students.pre-enrollments', compact('cursuses', 'preEnrollment', 'canModify'));
    }

    /**
     * Enregistre ou met à jour une pré-inscription.
     */
    public function store(StorePreEnrollmentRequest $request)
    {
        $data = $request->validated();

        // Associer à l'utilisateur connecté
        $data['user_id'] = Auth::id();

        // Vérifier si l'utilisateur a déjà une pré-inscription non modifiable
        $existingPreEnrollment = PreEnrollment::where('user_id', Auth::id())->first();
        if ($existingPreEnrollment && !$existingPreEnrollment->canBeModified()) {
            return redirect()->back()->with('error', 'Votre pré-inscription est déjà validée et ne peut plus être modifiée.');
        }

        // Gérer la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($existingPreEnrollment && $existingPreEnrollment->photo) {
                Storage::disk('public')->delete($existingPreEnrollment->photo);
            }
            $data['photo'] = $request->file('photo')->store('pre-enrollments/photos', 'public');
        }

        // Structurer le cursus scolaire
        $data['cursus_scolaire'] = [
            '2024-2025' => $request->input('cursus_2024_2025'),
            '2023-2024' => $request->input('cursus_2023_2024'),
            '2022-2023' => $request->input('cursus_2022_2023'),
            '2021-2022' => $request->input('cursus_2021_2022'),
        ];

        // Structurer le mode de paiement
        $data['mode_paiement'] = $request->input('paiement', []);

        // Récupérer l'année académique en cours
        $data['academic_year_id'] = AcademicYear::where('is_current', true)->value('id');

        // Déclaration sur l'honneur
        $data['declaration_honneur'] = $request->has('declaration_honneur');

        // Statut par défaut
        $data['status'] = 'en_attente';

        // Vérifier si une pré-inscription existe déjà pour cet utilisateur
        $preEnrollment = PreEnrollment::where('user_id', Auth::id())->first();

        if ($preEnrollment) {
            $preEnrollment->update($data);
            $message = 'Votre pré-inscription a été mise à jour avec succès.';
        } else {
            $preEnrollment = PreEnrollment::create($data);
            $message = 'Votre pré-inscription a été enregistrée avec succès.';
        }

        // Mettre à jour l'utilisateur avec les informations de la pré-inscription
        $user = Auth::user();
        $user->update([
            'first_name' => $data['prenom'] ?? $user->first_name,
            'last_name' => $data['nom'] ?? $user->last_name,
            'phone' => $data['telephone'] ?? $user->phone,
            'email' => $data['email'] ?? $user->email,
        ]);

        // Rediriger vers la page de succès avec le message
        return redirect()->route('pre-enrollments.success', $preEnrollment)
            ->with('success', $message);
    }

    /**
     * Affiche la page de confirmation après pré-inscription.
     */
    public function success(PreEnrollment $preEnrollment)
    {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($preEnrollment->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette page.');
        }

        $preEnrollment->load(['cursus', 'field', 'speciality', 'level']);

        $photoData = $this->photoAsBase64($preEnrollment);

        // Vérifier si une inscription existe déjà
        $enrollment = Enrollment::where('user_id', Auth::id())->first();

        return view('students.pre-enrollment-success', compact('preEnrollment', 'photoData', 'enrollment'));
    }

    /**
     * Télécharge le PDF récapitulatif de la pré-inscription.
     */
    public function downloadPdf(PreEnrollment $preEnrollment)
    {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($preEnrollment->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à télécharger ce document.');
        }

        $preEnrollment->load(['cursus', 'field', 'speciality', 'level', 'academicYear']);

        $photoData = $this->photoAsBase64($preEnrollment);

        $pdf = Pdf::loadView('students.pre-enrollment-pdf', compact('preEnrollment', 'photoData'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('pre-inscription-' . $preEnrollment->id . '.pdf');
    }

    /**
     * Supprime une pré-inscription.
     * Seulement si elle n'est pas validée.
     */
    public function destroy(PreEnrollment $preEnrollment)
    {
        // Vérifier que l'utilisateur est bien le propriétaire
        if ($preEnrollment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Vérifier si la pré-inscription peut être supprimée
        if (!$preEnrollment->canBeModified()) {
            return redirect()->back()->with('error', 'Cette pré-inscription ne peut pas être supprimée car elle est déjà validée.');
        }

        // Supprimer la photo si elle existe
        if ($preEnrollment->photo && Storage::disk('public')->exists($preEnrollment->photo)) {
            Storage::disk('public')->delete($preEnrollment->photo);
        }

        $preEnrollment->delete();

        return redirect()->route('pre-enrollments.index')
            ->with('success', 'Pré-inscription supprimée avec succès.');
    }

    /**
     * Encode la photo en base64 (data URI).
     * Utilisable directement dans un <img src="..."> dans HTML ou PDF.
     */
    private function photoAsBase64(PreEnrollment $preEnrollment): ?string
    {
        if (! $preEnrollment->photo || ! Storage::disk('public')->exists($preEnrollment->photo)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($preEnrollment->photo);
        $contents = Storage::disk('public')->get($preEnrollment->photo);

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    /**
     * Vérifie si l'utilisateur a déjà une pré-inscription.
     */
    public function checkStatus()
    {
        $user = Auth::user();
        $preEnrollment = $user->preEnrollment;
        $enrollment = $user->enrollment;

        $data = [
            'has_pre_enrollment' => $preEnrollment !== null,
            'pre_enrollment_status' => $preEnrollment?->status ?? null,
            'pre_enrollment_can_modify' => $preEnrollment?->canBeModified() ?? true,
            'has_enrollment' => $enrollment !== null,
            'enrollment_status' => $enrollment?->status ?? null,
            'matricule' => $enrollment?->matricule ?? null,
            'is_enrolled' => $enrollment?->isEnrolled() ?? false,
            'has_paid_registration' => $enrollment?->hasPaidRegistrationFee() ?? false,
        ];

        return response()->json($data);
    }

    /**
     * Valide la pré-inscription (pour l'admin).
     */
    public function approve(PreEnrollment $preEnrollment)
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // Valider la pré-inscription
        $preEnrollment->validate();

        // Créer automatiquement l'inscription
        $enrollment = Enrollment::createFromPreEnrollment($preEnrollment);

        return redirect()->back()->with('success', 'Pré-inscription validée et inscription créée.');
    }
}
