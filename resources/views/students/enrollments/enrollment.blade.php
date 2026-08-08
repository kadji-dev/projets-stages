@extends('layouts.student')

@section('title', 'Campus360 | Inscription Officielle')
@section('page-title', 'Inscription Officielle')

@section('content')
<section class="flex-1 p-4 md:p-12 space-y-8 max-w-[1440px] mx-auto w-full">

    @if(!$enrollment || $enrollment->status !== 'enrolled')

        {{-- État bloqué --}}
        <div class="bg-white rounded-3xl p-8 border border-zinc-100 shadow-sm text-center max-w-xl mx-auto space-y-6 my-12">
            <div class="w-16 h-16 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-3xl">lock</span>
            </div>

            <div class="space-y-2">
                <h3 class="font-montserrat text-2xl font-bold text-zinc-900">Inscription en attente</h3>
                <p class="font-inter text-xs text-zinc-500 leading-relaxed">
                    @if(!$preEnrollment)
                        Vous devez d'abord remplir votre dossier de pré-inscription avant d'accéder à cette étape.
                    @elseif($preEnrollment && $preEnrollment->isPending())
                        Votre pré-inscription est en cours de validation par l'administration.
                    @elseif($enrollment && !$enrollment->hasPaidRegistrationFee())
                        Votre dossier est pré-inscrit. Veuillez vous acquitter des frais d'inscription (30 000 FCFA) pour débloquer votre matricule.
                    @else
                        Votre dossier est en attente de validation définitive.
                    @endif
                </p>
            </div>

            @if(!$preEnrollment)
                <a href="{{ route('pre-enrollments.index') }}" class="inline-flex items-center gap-2 bg-[#af101a] text-white px-6 py-3.5 rounded-2xl font-inter text-xs font-bold hover:bg-[#8e0d15] transition">
                    <span>Remplir la pré-inscription</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            @elseif($preEnrollment && $preEnrollment->isPending())
                <div class="inline-flex items-center gap-2 bg-amber-500/10 text-amber-700 px-6 py-3.5 rounded-2xl font-inter text-xs font-bold">
                    <span class="material-symbols-outlined text-sm">hourglass_top</span>
                    <span>En attente de validation</span>
                </div>
            @elseif($enrollment && !$enrollment->hasPaidRegistrationFee())
                <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 bg-[#af101a] text-white px-6 py-3.5 rounded-2xl font-inter text-xs font-bold hover:bg-[#8e0d15] transition">
                    <span class="material-symbols-outlined text-sm">payments</span>
                    <span>Payer les 30 000 FCFA</span>
                </a>
            @else
                <div class="inline-flex items-center gap-2 bg-blue-500/10 text-blue-700 px-6 py-3.5 rounded-2xl font-inter text-xs font-bold">
                    <span class="material-symbols-outlined text-sm">pending</span>
                    <span>En attente de validation finale</span>
                </div>
            @endif

            {{-- Afficher le statut de la pré-inscription si elle existe --}}
            @if($preEnrollment)
                <div class="pt-4 border-t border-zinc-100">
                    <p class="font-inter text-xs text-zinc-400">
                        Statut de votre pré-inscription :
                        <span class="font-bold {{ $preEnrollment->isPending() ? 'text-amber-600' : ($preEnrollment->isValidated() ? 'text-emerald-600' : 'text-red-600') }}">
                            {{ $preEnrollment->status_label }}
                        </span>
                    </p>
                    @if($preEnrollment->canBeModified())
                        <a href="{{ route('pre-enrollments.index') }}" class="inline-flex items-center gap-1 text-[#af101a] font-bold text-xs hover:underline mt-2">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            <span>Modifier ma pré-inscription</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

    @else

        {{-- État inscrit : matricule affiché --}}
        <div class="space-y-8">

            {{-- Badge d'inscription avec Matricule --}}
            <div class="bg-gradient-to-r from-zinc-900 via-zinc-800 to-zinc-900 rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <span class="inline-flex items-center gap-2 bg-[#006444]/20 text-[#00c885] border border-[#006444]/30 px-3 py-1 rounded-xl font-inter text-xs font-bold mb-3">
                        <span class="material-symbols-outlined text-sm">verified</span> Inscription Définitive Validée
                    </span>
                    <p class="font-inter text-xs text-zinc-400 font-semibold uppercase tracking-widest">Matricule Étudiant</p>
                    <h2 class="font-montserrat text-3xl md:text-5xl font-black text-white tracking-wider mt-1">
                        {{ $enrollment->matricule }}
                    </h2>
                </div>

                <div class="text-left md:text-right">
                    <p class="font-inter text-[10px] text-zinc-400 uppercase tracking-widest">Date de validation</p>
                    <p class="font-montserrat text-sm font-bold text-white">{{ $enrollment->updated_at->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Fiche Récapitulative --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-100 shadow-sm space-y-6">
                <h4 class="font-montserrat text-lg font-bold text-zinc-900 border-b border-zinc-100 pb-4">Informations du Compte Académique</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-inter text-xs">
                    <div>
                        <span class="text-zinc-400 font-semibold text-[10px] uppercase">Nom & Prénom</span>
                        <p class="font-bold text-zinc-900 text-sm mt-0.5">{{ $user->full_name ?? auth()->user()->name }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-semibold text-[10px] uppercase">Filière</span>
                        <p class="font-bold text-zinc-900 text-sm mt-0.5">{{ $enrollment->program ?? $enrollment->specialty }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-semibold text-[10px] uppercase">Niveau</span>
                        <p class="font-bold text-zinc-900 text-sm mt-0.5">{{ $enrollment->level ?? $enrollment->degree_type }}</p>
                    </div>
                </div>
            </div>

            {{-- Progression des paiements --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-100 shadow-sm space-y-4">
                <h4 class="font-montserrat text-lg font-bold text-zinc-900 border-b border-zinc-100 pb-4">Progression des paiements</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <span class="text-zinc-400 font-semibold text-[10px] uppercase">Total payé</span>
                        <p class="font-bold text-[#006444] text-lg mt-0.5">{{ number_format($enrollment->totalPaidTuition(), 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-semibold text-[10px] uppercase">Frais d'inscription</span>
                        <p class="font-bold {{ $enrollment->hasPaidRegistrationFee() ? 'text-[#006444]' : 'text-amber-600' }} text-lg mt-0.5">
                            {{ $enrollment->hasPaidRegistrationFee() ? '✅ Réglé' : '⏳ En attente' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-semibold text-[10px] uppercase">Éligibilité Laptop</span>
                        <p class="font-bold {{ $enrollment->isLaptopEligible() ? 'text-[#006444]' : 'text-zinc-500' }} text-lg mt-0.5">
                            {{ $enrollment->isLaptopEligible() ? '✅ Éligible' : '⏳ En cours' }}
                            <span class="text-xs text-zinc-400 block">{{ number_format($enrollment->laptopProgressAmount(), 0, ',', ' ') }} / 150 000 FCFA</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Liens d'action --}}
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('payments.index') }}" class="inline-flex items-center justify-center gap-2 bg-[#af101a] text-white px-6 py-3 rounded-2xl font-inter text-sm font-bold hover:bg-[#8e0d15] transition">
                    <span class="material-symbols-outlined text-sm">payments</span>
                    <span>Continuer vers les paiements</span>
                </a>
                <a href="{{ route('student-dashboard.dashboard') }}" class="inline-flex items-center justify-center gap-2 border border-zinc-200 text-zinc-700 px-6 py-3 rounded-2xl font-inter text-sm font-bold hover:bg-zinc-50 transition">
                    <span class="material-symbols-outlined text-sm">dashboard</span>
                    <span>Tableau de bord</span>
                </a>
                @if($enrollment->isLaptopEligible())
                    <a href="#" class="inline-flex items-center justify-center gap-2 bg-[#006444] text-white px-6 py-3 rounded-2xl font-inter text-sm font-bold hover:bg-[#005237] transition">
                        <span class="material-symbols-outlined text-sm">laptop_mac</span>
                        <span>Demander mon laptop</span>
                    </a>
                @endif
            </div>
        </div>

    @endif

</section>
@endsection
