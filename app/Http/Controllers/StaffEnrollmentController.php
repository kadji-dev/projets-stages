<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\PreEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $totalApplicants = Enrollment::count();
        $pendingCount = Enrollment::whereIn('status', ['pending', 'in_progress'])->count();
        $enrolledCount = Enrollment::where('status', 'enrolled')->count();
        $laptopEligibleCount = Enrollment::where('laptop_eligible', true)->count();

        $query = Enrollment::with('user', 'preEnrollment');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('preEnrollment', function($pq) use ($search) {
                      $pq->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'laptop_eligible') {
                $query->where('laptop_eligible', true);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        if ($request->filled('registration_status')) {
            if ($request->registration_status === 'paid') {
                $query->whereHas('payments', function($pq) {
                    $pq->where('type', 'inscription')
                       ->whereIn('status', ['approved', 'completed']);
                });
            } elseif ($request->registration_status === 'unpaid') {
                $query->whereDoesntHave('payments', function($pq) {
                    $pq->where('type', 'inscription')
                       ->whereIn('status', ['approved', 'completed']);
                });
            }
        }

        $enrollments = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => $totalApplicants,
            'pending' => $pendingCount,
            'enrolled' => $enrolledCount,
            'laptop_eligible' => $laptopEligibleCount,
            'registration_paid' => Enrollment::whereHas('payments', function($q) {
                $q->where('type', 'inscription')
                  ->whereIn('status', ['approved', 'completed']);
            })->count(),
            'registration_unpaid' => Enrollment::whereDoesntHave('payments', function($q) {
                $q->where('type', 'inscription')
                  ->whereIn('status', ['approved', 'completed']);
            })->count(),
        ];

        // ✅ Passer toutes les variables pour compatibilité avec la vue
        return view('admissions.admission', compact(
            'totalApplicants',
            'pendingCount',
            'enrolledCount',
            'laptopEligibleCount',
            'enrollments',
            'stats'
        ));
    }

    /**
     * Enregistre un nouvel étudiant.
     * Crée automatiquement un compte utilisateur si l'email n'existe pas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:enrollments,email',
            'phone'   => 'required|string|max:20',
            'program' => 'required|string',
            'level'   => 'required|string',
            'pre_enrollment_id' => 'nullable|exists:pre_enrollments,id',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $password = Str::random(10);
        $isNewUser = false;

        $nameParts = explode(' ', $validated['name']);
        $firstName = $nameParts[0] ?? '';
        $lastName = !empty($nameParts) ? array_pop($nameParts) : '';

        if (!$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => 'student',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $validated['phone'],
                'status' => 'pending'
            ]);
            $isNewUser = true;
        }

        $preEnrollmentId = $validated['pre_enrollment_id'] ?? null;
        if ($preEnrollmentId) {
            $preEnrollment = PreEnrollment::find($preEnrollmentId);
            if ($preEnrollment && $preEnrollment->user_id === $user->id) {
                $preEnrollment->status = 'validee';
                $preEnrollment->save();
            }
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'program' => $validated['program'],
            'level' => $validated['level'],
            'degree_type' => $validated['level'] ?? 'Bac+2',
            'specialty' => $validated['program'] ?? 'Général',
            'pre_enrollment_id' => $preEnrollmentId,
            'status' => 'pending',
            'laptop_eligible' => false,
        ]);

        $message = 'Étudiant enregistré avec succès.';
        if ($isNewUser) {
            $message .= " Un compte a été créé avec le mot de passe : <strong>{$password}</strong>";
        }
        if ($preEnrollmentId) {
            $message .= " Pré-inscription liée et validée.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Valide l'inscription d'un étudiant et génère son matricule.
     */
    public function approve(Enrollment $enrollment)
    {
        if (!$enrollment->hasPaidRegistrationFee()) {
            return redirect()->back()->with('error', 'Les frais d\'inscription (30 000 FCFA) doivent être payés avant la validation.');
        }

        $enrollment->validateEnrollment();

        return redirect()->back()->with('success', "✅ Inscription validée ! Matricule : {$enrollment->matricule}");
    }

    /**
     * Met à jour les informations de l'étudiant.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:enrollments,email,' . $enrollment->id,
            'phone' => 'required|string|max:20',
            'program' => 'nullable|string',
            'level' => 'nullable|string',
        ]);

        $enrollment->update($validated);

        if ($enrollment->user) {
            $nameParts = explode(' ', $validated['name']);
            $firstName = $nameParts[0] ?? '';
            $lastName = !empty($nameParts) ? array_pop($nameParts) : '';

            $enrollment->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'first_name' => $firstName,
                'last_name' => $lastName
            ]);
        }

        return redirect()->back()->with('success', 'Informations mises à jour.');
    }

    /**
     * Supprime un étudiant.
     */
    public function destroy(Enrollment $enrollment)
    {
        $user = $enrollment->user;

        $enrollment->delete();

        if ($user && $user->isStudent()) {
            $user->delete();
        }

        return redirect()->back()->with('success', 'Étudiant supprimé avec succès.');
    }

    /**
     * Génère un matricule pour un étudiant (manuel).
     */
    public function generateMatricule(Enrollment $enrollment)
    {
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

    /**
     * Exporte les étudiants en CSV.
     */
    public function export(Request $request)
    {
        $query = Enrollment::with('user', 'payments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->get();

        $filename = 'etudiants_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Matricule', 'Nom', 'Email', 'Téléphone', 'Statut', 'Total Payé', 'Éligible Laptop']);

        foreach ($enrollments as $enrollment) {
            fputcsv($handle, [
                $enrollment->matricule ?? 'Non attribué',
                $enrollment->full_name,
                $enrollment->email,
                $enrollment->phone,
                $enrollment->status_label,
                $enrollment->totalPaidTuition(),
                $enrollment->isLaptopEligible() ? 'Oui' : 'Non'
            ]);
        }

        fclose($handle);

        return response()->download(
            $filename,
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }
}
