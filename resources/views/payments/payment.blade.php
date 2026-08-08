@extends('layouts.staff')

@section('title', 'Paiements | Campus360 Staff')
@section('page-title', 'Paiements')

@section('content')
<div x-data="{ showAddPaymentModal: false }" class="p-4 md:p-12 space-y-8">

    {{-- KPIs FINANCIERS DYNAMIQUES --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-zinc-400 uppercase tracking-wider">TOTAL ENCAISSÉ</p>
                <h3 class="text-2xl font-montserrat font-bold text-emerald-600 mt-1">{{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-amber-600 uppercase tracking-wider">PAIEMENTS EN ATTENTE</p>
                <h3 class="text-2xl font-montserrat font-bold text-amber-600 mt-1">{{ number_format($pendingAmount, 0, ',', ' ') }} FCFA</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined">pending</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-indigo-600 uppercase tracking-wider">ÉTU. À JOUR (TRANCHE 1)</p>
                <h3 class="text-2xl font-montserrat font-bold text-indigo-600 mt-1">{{ $upToDateCount }} / {{ $totalEnrolled }}</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
        </div>
    </div>

    {{-- FILTRES & RECHERCHE --}}
    <form method="GET" action="{{ route('staff-dashboard.payments') }}" class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="relative w-full md:w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher étudiant, matricule, référence..." class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-[#af101a]">
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="method" onchange="this.form.submit()" class="px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-inter text-zinc-700">
                <option value="">Tous les modes</option>
                <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Espèces (Guichet)</option>
                <option value="om" {{ request('method') == 'om' ? 'selected' : '' }}>Orange Money</option>
                <option value="momo" {{ request('method') == 'momo' ? 'selected' : '' }}>MTN Mobile Money</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-inter text-zinc-700">
                <option value="">Tous les statuts</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Payé / Validé</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>À valider</option>
            </select>

            <button type="button" @click="showAddPaymentModal = true" class="flex items-center gap-2 px-5 py-2.5 bg-[#af101a] text-white rounded-xl text-sm font-semibold hover:bg-[#8f0d15] transition-colors cursor-pointer shadow-md shadow-[#af101a]/20">
                <span class="material-symbols-outlined text-lg">add_card</span>
                <span>Nouveau Paiement Cash</span>
            </button>
        </div>
    </form>

    {{-- TABLEAU DYNAMIQUE --}}
    <div class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50/50 border-b border-zinc-100 font-montserrat text-xs text-zinc-400 uppercase tracking-wider">
                        <th class="p-4 px-6">ÉTU.</th>
                        <th class="p-4 px-6">MATRICULE</th>
                        <th class="p-4 px-6">MONTANT VERSÉ</th>
                        <th class="p-4 px-6">MOTIF / TRANCHE</th>
                        <th class="p-4 px-6">MODE & RÉFÉRENCE</th>
                        <th class="p-4 px-6">DATE</th>
                        <th class="p-4 px-6">STATUT</th>
                        <th class="p-4 px-6 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 font-inter text-sm text-zinc-700">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="p-4 px-6">
                            <div class="font-bold text-zinc-900">
                                {{ $payment->enrollment?->name ?? trim(($payment->enrollment?->first_name ?? '') . ' ' . ($payment->enrollment?->last_name ?? '')) }}
                            </div>
                            <div class="text-xs text-zinc-400">{{ $payment->enrollment?->program }}</div>
                        </td>
                        <td class="p-4 px-6 font-mono text-xs font-bold text-zinc-800">
                            {{ $payment->enrollment?->matricule ?? 'Non attribué' }}
                        </td>
                        <td class="p-4 px-6 font-bold text-zinc-900">
                            {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="p-4 px-6">
                            <span class="text-xs font-semibold px-2 py-1 bg-zinc-100 rounded text-zinc-700">{{ $payment->type }}</span>
                        </td>
                        <td class="p-4 px-6">
                            <div class="font-medium text-xs text-zinc-800">{{ strtoupper($payment->payment_method) }}</div>
                            <div class="font-mono text-[11px] text-zinc-400">{{ $payment->reference }}</div>
                        </td>
                        <td class="p-4 px-6 text-xs text-zinc-500">{{ $payment->created_at->format('d M. Y') }}</td>
                        <td class="p-4 px-6">
                            @if($payment->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Validé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> À valider
                                </span>
                            @endif
                        </td>
                        <td class="p-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($payment->status === 'pending')
                                <form method="POST" action="{{ route('staff.payments.approve', $payment->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        <span>Confirmer</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-zinc-400">Aucun paiement trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-zinc-100">
            {{ $payments->links() }}
        </div>
    </div>

    {{-- MODALE ENCAISSEMENT CASH --}}
    <div x-show="showAddPaymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.away="showAddPaymentModal = false" class="bg-white rounded-2xl shadow-2xl border border-zinc-100 w-full max-w-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex justify-between items-center bg-zinc-50">
                <h3 class="font-montserrat font-bold text-zinc-900 text-lg">Enregistrer un versement Cash</h3>
                <button @click="showAddPaymentModal = false" class="text-zinc-400 hover:text-zinc-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" action="{{ route('staff.payments.storeCash') }}" class="p-6 space-y-4 text-sm">
                @csrf
                <div>
                    <label class="block font-semibold text-zinc-700 mb-1">Étudiant *</label>
                    <select name="enrollment_id" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                        <option value="">Sélectionner un étudiant...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                                ({{ $student->matricule ?? 'Sans matricule' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Motif / Tranche *</label>
                        <select name="type" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                            <option value="1ère Tranche Scolarité">1ère Tranche Scolarité</option>
                            <option value="2ème Tranche Scolarité">2ème Tranche Scolarité</option>
                            <option value="Frais de pré-inscription">Frais de pré-inscription</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Montant Reçu (FCFA) *</label>
                        <input type="number" name="amount" required placeholder="Ex: 150000" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl font-bold">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-zinc-700 mb-1">N° Référence Reçu</label>
                    <input type="text" name="reference" placeholder="Optionnel (auto-généré si vide)" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" @click="showAddPaymentModal = false" class="px-4 py-2 border rounded-xl text-zinc-600">Annuler</button>
                    <button type="submit" class="px-5 py-2 bg-[#af101a] text-white rounded-xl font-semibold">Valider & Encaisser</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
