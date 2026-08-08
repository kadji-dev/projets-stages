@extends('layouts.student')

@section('title', 'Campus360 | Paiements & Scolarité')
@section('page-title', 'Paiements')

@section('content')
<section class="flex-1 p-4 md:p-12 space-y-8 max-w-[1440px] mx-auto w-full"
         x-data="paymentApp()"
         x-init="init()">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-inter text-xs text-[#af101a] font-extrabold uppercase tracking-[0.2em]">Scolarité & Dotation</p>
            <h3 class="font-montserrat text-3xl md:text-4xl font-extrabold text-zinc-900 tracking-tight mt-1">
                Paiements <span class="text-[#af101a]/20">/</span> Scolarité & Laptop
            </h3>
            <p class="font-inter text-sm text-zinc-500 mt-1 max-w-xl">
                Suivez l'état de votre pension (400 000 FCFA) et débloquez votre ordinateur portable dès 150 000 FCFA versés.
            </p>
        </div>

        <div>
            <div x-show="inscriptionStatus === 'validated'"
                 class="inline-flex items-center gap-2 bg-[#006444]/10 border border-[#006444]/20 px-4 py-2 rounded-2xl text-[#006444] font-inter text-xs font-bold shadow-sm">
                <span class="material-symbols-outlined text-base">verified</span>
                <span>Inscription Validée (30 000 FCFA)</span>
            </div>
            <div x-show="inscriptionStatus !== 'validated'"
                 class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/20 px-4 py-2 rounded-2xl text-amber-700 font-inter text-xs font-bold shadow-sm">
                <span class="material-symbols-outlined text-base">pending</span>
                <span>Inscription requise (30 000 FCFA)</span>
            </div>
        </div>
    </div>

    {{-- Messages --}}
    <div x-show="successMessage" x-transition
         class="bg-[#006444]/10 border border-[#006444]/30 rounded-2xl p-4 flex items-center justify-between text-[#006444] font-inter text-sm font-semibold">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-xl">check_circle</span>
            <span x-text="successMessage"></span>
        </div>
        <button @click="successMessage = ''" class="text-[#006444]/60 hover:text-[#006444]">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>

    <div x-show="errorMessage" x-transition
         class="bg-[#af101a]/10 border border-[#af101a]/30 rounded-2xl p-4 flex items-center justify-between text-[#af101a] font-inter text-sm font-semibold">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-xl">error</span>
            <span x-text="errorMessage"></span>
        </div>
        <button @click="errorMessage = ''" class="text-[#af101a]/60 hover:text-[#af101a]">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-6 border border-zinc-100 shadow-sm space-y-2">
            <span class="font-inter text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Frais d'Inscription</span>
            <div class="flex items-baseline justify-between">
                <p class="font-montserrat text-2xl font-extrabold text-zinc-900">30 000 <span class="text-xs text-zinc-400 font-normal">FCFA</span></p>
                <span class="text-xs font-bold"
                      :class="inscriptionStatus === 'validated' ? 'text-[#006444]' : 'text-amber-600'"
                      x-text="inscriptionStatus === 'validated' ? 'Réglé' : 'Non Réglé'"></span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-zinc-100 shadow-sm space-y-2">
            <span class="font-inter text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Pension Totale</span>
            <div class="flex items-baseline justify-between">
                <p class="font-montserrat text-2xl font-extrabold text-zinc-900">
                    <span x-text="formatNumber(totalPensionPaid)"></span>
                    <span class="text-xs text-zinc-400 font-normal">/ 400 000 FCFA</span>
                </p>
                <span class="text-xs font-bold text-[#af101a]" x-text="pensionPercent + '%'"></span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-zinc-100 shadow-sm space-y-2 sm:col-span-2 lg:col-span-1">
            <span class="font-inter text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Seuil Laptop (1ère Tranche)</span>
            <div class="flex items-baseline justify-between">
                <p class="font-montserrat text-2xl font-extrabold text-[#af101a]">
                    <span x-text="formatNumber(laptopPaidAmount)"></span>
                    <span class="text-xs text-zinc-400 font-normal">/ 150 000 FCFA</span>
                </p>
                <span class="text-xs font-bold"
                      :class="laptopPaidAmount >= 150000 ? 'text-[#006444]' : 'text-amber-600'"
                      x-text="laptopPaidAmount >= 150000 ? 'Débloqué' : 'En cours'"></span>
            </div>
        </div>
    </div>

    {{-- Progression Laptop + Formulaire --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Progression --}}
        <div class="lg:col-span-8 bg-white rounded-3xl p-8 border border-zinc-100 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-zinc-100 text-zinc-600 font-inter text-[10px] font-bold uppercase tracking-wider mb-2">Dotation Matérielle</span>
                    <h4 class="font-montserrat text-xl font-bold text-zinc-900">Progression du Retrait Laptop</h4>
                    <p class="font-inter text-xs text-zinc-400 mt-1">Atteignez 150 000 FCFA de versement sur la pension pour retirer votre ordinateur.</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="font-montserrat text-3xl font-extrabold text-[#af101a]">
                        <span x-text="formatNumber(laptopPaidAmount)"></span>
                        <span class="text-xs text-zinc-400 font-normal">FCFA</span>
                    </p>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between font-inter text-xs font-bold text-zinc-600">
                    <span>Avancement (1ère Tranche : 150 000 FCFA)</span>
                    <span class="text-[#af101a]" x-text="laptopPercentage + '%'"></span>
                </div>
                <div class="w-full bg-zinc-100 rounded-full h-4 overflow-hidden p-0.5 border border-zinc-200/60">
                    <div class="bg-[#af101a] h-full rounded-full transition-all duration-700 shadow-md shadow-[#af101a]/30"
                         :style="'width: ' + laptopPercentage + '%'"></div>
                </div>
            </div>

            <div x-show="laptopPaidAmount >= 150000" x-transition
                 class="bg-[#006444]/5 border border-[#006444]/20 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#006444] text-white flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl">laptop_mac</span>
                    </div>
                    <div>
                        <h5 class="font-montserrat font-bold text-[#006444] text-sm">Seuil de 150 000 FCFA atteint !</h5>
                        <p class="font-inter text-xs text-zinc-500">Votre ordinateur portable est prêt à être récupéré.</p>
                    </div>
                </div>
                <a href="#" class="bg-[#006444] text-white px-5 py-3 rounded-xl font-inter text-xs font-bold inline-flex items-center gap-2 hover:bg-[#005237] transition shadow-lg shadow-[#006444]/20 shrink-0">
                    <span>Fiche d'émargement</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <div x-show="laptopPaidAmount < 150000" class="bg-zinc-50 border border-zinc-200/80 rounded-2xl p-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-zinc-400 text-xl">info</span>
                <p class="font-inter text-xs text-zinc-500">
                    Reste à verser pour le laptop :
                    <strong class="text-zinc-800" x-text="formatNumber(Math.max(0, 150000 - laptopPaidAmount)) + ' FCFA'"></strong>
                </p>
            </div>
        </div>

        {{-- Formulaire de paiement --}}
        <div class="lg:col-span-4 bg-white rounded-3xl p-8 border border-zinc-100 shadow-sm space-y-6">
            <div>
                <h4 class="font-montserrat text-lg font-bold text-zinc-900">Nouveau Versement</h4>
                <p class="font-inter text-xs text-zinc-400 mt-1">Paiement sécurisé via GeniusPay</p>
            </div>

            {{-- Formulaire avec Alpine.js --}}
            <form @submit.prevent="submitPayment" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label class="font-inter text-xs font-bold text-zinc-700">Motif du versement</label>
                    <select x-model="paymentType"
                            class="w-full text-xs font-inter rounded-xl border-zinc-200 bg-zinc-50/50 p-3 text-zinc-800 focus:border-[#af101a] focus:ring-[#af101a]">
                        <template x-if="inscriptionStatus !== 'validated'">
                            <option value="inscription">Frais d'inscription (30 000 FCFA)</option>
                        </template>
                        <option value="pension_tranche1">Pension - 1ère Tranche Laptop (Objectif 150 000 FCFA)</option>
                        <option value="pension_solde">Pension - Solde Escolarité (Total 400 000 FCFA)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="font-inter text-xs font-bold text-zinc-700">Montant (FCFA)</label>
                    <input type="number"
                           x-model="amount"
                           min="1000"
                           placeholder="Ex: 50000"
                           required
                           class="w-full text-xs font-inter rounded-xl border-zinc-200 bg-zinc-50/50 p-3 text-zinc-800 focus:border-[#af101a] focus:ring-[#af101a]">
                    <p class="font-inter text-[10px] text-zinc-400">Montant libre à partir de 1 000 FCFA</p>
                </div>

                <button type="submit"
                        :disabled="isLoading || amount < 1000"
                        class="w-full bg-[#af101a] text-white py-4 rounded-2xl font-inter text-xs font-bold flex items-center justify-center gap-2 hover:bg-[#8e0d15] hover:shadow-xl hover:shadow-[#af101a]/20 transition active:scale-95 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!isLoading">Procéder au paiement</span>
                    <span x-show="isLoading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Traitement...
                    </span>
                    <span class="material-symbols-outlined text-sm" x-show="!isLoading">payments</span>
                </button>
            </form>

            {{-- Message d'information sur le mode simulation --}}
            <div class="text-center">
                <p class="font-inter text-[10px] text-zinc-400">
                    🔒 Paiement sécurisé - Mode simulation activé
                </p>
            </div>
        </div>
    </div>

    {{-- Historique --}}
    <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
            <h4 class="font-montserrat text-base font-bold text-zinc-900">Historique des versements</h4>
            <span class="font-inter text-xs text-zinc-400">Reçus officiels</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left font-inter text-xs">
                <thead class="bg-zinc-50/80 text-zinc-400 uppercase text-[10px] tracking-wider font-bold border-b border-zinc-100">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Motif</th>
                        <th class="px-6 py-4">Mode</th>
                        <th class="px-6 py-4">Montant</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Reçu PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 text-zinc-700">
                    <template x-if="payments.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-400 font-inter text-xs">
                                Aucun versement trouvé pour le moment.
                            </td>
                        </tr>
                    </template>
                    <template x-for="payment in payments" :key="payment.id">
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-zinc-900" x-text="payment.reference"></td>
                            <td class="px-6 py-4 font-medium" x-text="getTypeLabel(payment.type)"></td>
                            <td class="px-6 py-4 uppercase font-semibold text-[10px] text-zinc-500" x-text="payment.payment_method || 'En ligne'"></td>
                            <td class="px-6 py-4 font-bold text-zinc-900" x-text="formatNumber(payment.amount) + ' FCFA'"></td>
                            <td class="px-6 py-4">
                                <span x-show="payment.status === 'completed' || payment.status === 'approved'"
                                      class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#006444]/10 text-[#006444] font-bold text-[10px]">Validé</span>
                                <span x-show="payment.status === 'pending'"
                                      class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-700 font-bold text-[10px]">En attente</span>
                                <span x-show="payment.status !== 'completed' && payment.status !== 'approved' && payment.status !== 'pending'"
                                      class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#af101a]/10 text-[#af101a] font-bold text-[10px]">Échoué</span>
                            </td>
                            <td class="px-6 py-4 text-zinc-400" x-text="formatDate(payment.created_at)"></td>
                            <td class="px-6 py-4 text-right">
                                <a x-show="payment.status === 'completed' || payment.status === 'approved'"
                                   :href="'/students/payments/' + payment.id + '/receipt'"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-[#af101a] font-bold hover:underline transition-colors">
                                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                                    <span>PDF</span>
                                </a>
                                <span x-show="payment.status !== 'completed' && payment.status !== 'approved'"
                                      class="text-zinc-300">—</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
function paymentApp() {
    return {
        // Données
        payments: @json($payments ?? []),
        inscriptionStatus: @json(optional($inscription ?? null)->status ?? 'pending'),
        totalPensionPaid: @json($totalPensionPaid ?? 0),
        laptopPaidAmount: @json($laptopPaidAmount ?? 0),
        paymentType: 'inscription',
        amount: 10000,
        isLoading: false,
        successMessage: '',
        errorMessage: '',

        // Computed
        get pensionPercent() {
            return Math.min(100, Math.round((this.totalPensionPaid / 400000) * 100));
        },
        get laptopPercentage() {
            return Math.min(100, Math.round((this.laptopPaidAmount / 150000) * 100));
        },

        // Méthodes
        init() {
            const success = @json(session('success'));
            const error = @json(session('error'));
            if (success) this.successMessage = success;
            if (error) this.errorMessage = error;
        },

        submitPayment() {
            if (!this.amount || this.amount < 1000) {
                this.errorMessage = 'Le montant minimum est de 1 000 FCFA.';
                return;
            }

            this.isLoading = true;
            this.errorMessage = '';
            this.successMessage = '';

            fetch('{{ route("payments.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    type: this.paymentType,
                    amount: this.amount
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isLoading = false;
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else if (data.success && data.message) {
                    this.successMessage = data.message;
                    // Recharger la page après 2 secondes pour voir les mises à jour
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.errorMessage = data.message || 'Une erreur est survenue.';
                }
            })
            .catch(error => {
                this.isLoading = false;
                this.errorMessage = 'Erreur de connexion. Veuillez réessayer.';
                console.error('Error:', error);
            });
        },

        formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        },

        formatDate(date) {
            if (!date) return '—';
            const d = new Date(date);
            return d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
        },

        getTypeLabel(type) {
            const labels = {
                'inscription': 'Frais d\'inscription',
                'pension_tranche1': 'Pension (1ère Tranche / PC)',
                'pension_solde': 'Pension (Scolarité)'
            };
            return labels[type] || type;
        }
    }
}
</script>
@endsection
