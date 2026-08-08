@extends('layouts.staff')

@section('title', 'Campus360 Admin | Paiements')

@section('content')

@php
    // Données de démonstration en attendant le contrôleur (mêmes noms de
    // variables qu'utilisera la logique réelle, pour un branchement direct).
    $stats = $stats ?? [
        'total_encaisse' => 4820000,
        'inscrits' => 128,
        'eligibles_laptop' => 6,
    ];

    $enrollments = $enrollments ?? collect([
        (object) [
            'id' => 1,
            'matricule' => 'ESC-2026-0001',
            'status' => 'inscrit',
            'laptop_status' => 'eligible',
            'total_paid' => 150000,
            'laptop_threshold' => 150000,
            'user' => (object) ['name' => 'Jean-Marc Koffi', 'email' => 'jean.koffi@Campus360.cm'],
        ],
        (object) [
            'id' => 2,
            'matricule' => 'ESC-2026-0002',
            'status' => 'inscrit',
            'laptop_status' => 'non_eligible',
            'total_paid' => 80000,
            'laptop_threshold' => 150000,
            'user' => (object) ['name' => 'Awa Traoré', 'email' => 'awa.traore@Campus360.cm'],
        ],
        (object) [
            'id' => 3,
            'matricule' => null,
            'status' => 'en_attente_frais',
            'laptop_status' => 'non_eligible',
            'total_paid' => 0,
            'laptop_threshold' => 150000,
            'user' => (object) ['name' => 'Yao Kouadio', 'email' => 'yao.kouadio@Campus360.cm'],
        ],
    ]);

    $recentPayments = $recentPayments ?? collect([
        (object) ['id' => 1, 'amount' => 30000, 'method' => 'geniuspay', 'created_at' => now()->subHours(2), 'enrollment' => (object) ['user' => (object) ['name' => 'Jean-Marc Koffi']]],
        (object) ['id' => 2, 'amount' => 50000, 'method' => 'cash', 'created_at' => now()->subHours(5), 'enrollment' => (object) ['user' => (object) ['name' => 'Awa Traoré']]],
    ]);

    $search = $search ?? null;
@endphp

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('staffPaymentsPage', () => ({
        cashModalOpen: false,
        selectedEnrollmentId: '',
        selectedEnrollmentName: '',
        amount: '',

        openCashModal(enrollmentId, name) {
            this.selectedEnrollmentId = enrollmentId;
            this.selectedEnrollmentName = name;
            this.amount = '';
            this.cashModalOpen = true;
        },
    }));
});
</script>

<div class="space-y-8" x-data="staffPaymentsPage()">
    <x-staff.flash />

    <x-staff.page-header
        title="Paiements"
        subtitle="Suivi en temps réel des règlements GeniusPay et Cash."
    >
        <x-slot:actions>
            <button type="button" @click="openCashModal('', '')" class="bg-[#af101a] text-white px-6 py-3 rounded-xl font-bold text-sm inline-flex items-center gap-2 shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">point_of_sale</span>
                Enregistrer un paiement Cash
            </button>
        </x-slot:actions>
    </x-staff.page-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-staff.stat-card label="Total encaissé" :value="number_format($stats['total_encaisse'], 0, ',', ' ').' XAF'" value-class="text-[#006444]" />
        <x-staff.stat-card label="Étudiants inscrits" :value="$stats['inscrits']" />
        <x-staff.stat-card label="Éligibles au retrait PC" :value="$stats['eligibles_laptop']" value-class="text-[#af101a]" />
    </div>

    <!-- Recherche + liste des étudiants -->
    <x-staff.table-card title="Suivi des étudiants" icon="groups">
        <x-slot:actions>
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Rechercher un étudiant (nom, email)..." class="bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-[#af101a]/20">
                <button type="submit" class="w-9 h-9 rounded-xl bg-zinc-100 flex items-center justify-center hover:bg-zinc-200">
                    <span class="material-symbols-outlined text-lg text-zinc-600">search</span>
                </button>
            </form>
        </x-slot:actions>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Étudiant</th>
                    <th class="px-6 py-4">Matricule</th>
                    <th class="px-6 py-4">Progression</th>
                    <th class="px-6 py-4">Statut</th>
                    <th class="px-6 py-4">Éligible PC</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($enrollments as $enrollment)
                    @php
                        $percent = $enrollment->laptop_threshold > 0
                            ? (int) min(100, round($enrollment->total_paid / $enrollment->laptop_threshold * 100))
                            : 0;
                    @endphp
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5">
                            <p class="font-montserrat font-bold text-zinc-900">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-zinc-400">{{ $enrollment->user->email }}</p>
                        </td>
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $enrollment->matricule ?? '—' }}</td>
                        <td class="px-6 py-5">
                            <div class="w-32 h-2 rounded-full bg-zinc-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $percent >= 100 ? 'bg-[#006444]' : 'bg-[#af101a]' }}" style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-[10px] text-zinc-400 mt-1">{{ number_format($enrollment->total_paid, 0, ',', ' ') }} / {{ number_format($enrollment->laptop_threshold, 0, ',', ' ') }} XAF</p>
                        </td>
                        <td class="px-6 py-5">
                            <x-staff.status-badge :status="$enrollment->status === 'inscrit' ? 'success' : 'warning'">
                                {{ $enrollment->status === 'inscrit' ? 'Inscrit' : 'En attente' }}
                            </x-staff.status-badge>
                        </td>
                        <td class="px-6 py-5">
                            @if ($enrollment->laptop_status === 'eligible')
                                <x-staff.status-badge status="success">Éligible</x-staff.status-badge>
                            @elseif ($enrollment->laptop_status === 'retire')
                                <x-staff.status-badge status="default">Retiré</x-staff.status-badge>
                            @else
                                <span class="text-zinc-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="openCashModal({{ $enrollment->id }}, {{ json_encode($enrollment->user->name) }})"
                                    class="px-4 py-2 rounded-lg border border-zinc-200 text-xs font-bold text-zinc-700 hover:border-[#af101a] hover:text-[#af101a] inline-flex items-center gap-1"
                                >
                                    <span class="material-symbols-outlined text-sm">add</span>
                                    Encaisser
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-400">Aucun étudiant trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <!-- Activité récente -->
    <x-staff.table-card title="Activité récente" icon="history">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Étudiant</th>
                    <th class="px-6 py-4">Montant</th>
                    <th class="px-6 py-4">Méthode</th>
                    <th class="px-6 py-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($recentPayments as $payment)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $payment->enrollment->user->name }}</td>
                        <td class="px-6 py-5 text-zinc-700">{{ number_format($payment->amount, 0, ',', ' ') }} XAF</td>
                        <td class="px-6 py-5">
                            <x-staff.status-badge :status="$payment->method === 'geniuspay' ? 'accent' : 'default'">
                                {{ $payment->method === 'geniuspay' ? 'GeniusPay' : 'Cash' }}
                            </x-staff.status-badge>
                        </td>
                        <td class="px-6 py-5 text-zinc-500 text-xs">{{ $payment->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-zinc-400">Aucun paiement récent.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <!-- Modale encaissement Cash -->
    <x-staff.modal title="Encaisser un paiement Cash">
        <form method="POST" action="#" class="space-y-4">
            @csrf

            <div class="space-y-2" x-show="!selectedEnrollmentId">
                <label class="text-xs font-semibold text-zinc-600">Rechercher l'étudiant</label>
                <input type="text" placeholder="Nom ou email de l'étudiant..." class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                <p class="text-xs text-zinc-400">La recherche en direct sera branchée avec la logique du contrôleur.</p>
            </div>

            <div class="space-y-2" x-show="selectedEnrollmentId">
                <label class="text-xs font-semibold text-zinc-600">Étudiant sélectionné</label>
                <div class="p-3 rounded-xl bg-[#af101a]/5 border border-[#af101a]/20 font-bold text-zinc-900" x-text="selectedEnrollmentName"></div>
                <input type="hidden" name="enrollment_id" x-model="selectedEnrollmentId">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Montant remis (XAF)</label>
                <input x-model="amount" name="amount" type="number" min="100" step="100" placeholder="Ex: 30000" class="w-full p-4 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20 font-bold text-lg">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="cashModalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Valider l'encaissement</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
