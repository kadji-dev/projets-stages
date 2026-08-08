@extends('layouts.student')

@section('title', 'Campus360 | Tableau de bord Étudiant')
@section('page-title', 'Tableau de bord')

@section('content')
<section class="flex-1 p-4 md:p-12 space-y-12 max-w-[1440px] mx-auto w-full">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-3">
        <p class="font-inter text-xs text-[#af101a] font-extrabold uppercase tracking-[0.2em]">Bienvenue sur votre portail</p>
        <h3 class="font-montserrat text-4xl md:text-5xl font-extrabold text-zinc-900 tracking-tight leading-[1.1]">
            Préparez votre rentrée <span class="text-[#af101a]/10 select-none">/</span> chez Campus360.
        </h3>
        <p class="font-inter text-base text-zinc-500 max-w-2xl leading-relaxed">
            Félicitations pour votre admission ! Suivez les étapes ci-dessous pour finaliser votre dossier et rejoindre notre communauté académique.
        </p>
    </div>

    <!-- Dashboard Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="flex flex-col gap-8 lg:col-span-12">
            <!-- Hero Action Card -->
            <div class="bg-white rounded-3xl p-8 md:p-12 relative overflow-hidden premium-shadow border border-zinc-100 group">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#af101a]/5 rounded-full blur-[100px] group-hover:bg-[#af101a]/10 transition-colors duration-700"></div>
                <div class="relative z-10">
                    <div class="mb-8">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#af101a]/10 text-[#af101a] font-inter text-[10px] font-bold uppercase tracking-wider">Action Requise</span>
                    </div>
                    <h4 class="font-montserrat text-2xl md:text-3xl font-bold text-zinc-900 mb-4">Finaliser votre pré-inscription</h4>
                    <p class="font-inter text-zinc-500 mb-10 max-w-lg leading-relaxed text-base">
                        Nous avons besoin de vos documents justificatifs (Diplôme, ID, Photo) pour valider votre dossier administratif avant le <span class="font-bold text-zinc-900">09 Septembre</span>.
                    </p>
                    <a href="{{ route('pre-enrollments.index') }}" class="bg-[#af101a] text-white px-10 py-5 rounded-2xl font-inter text-sm font-bold inline-flex items-center gap-3 hover:shadow-2xl hover:-translate-y-1 transition-all active:scale-95 shadow-xl shadow-[#af101a]/20 cursor-pointer">
                        Commencer ma Pré-inscription
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </a>
                </div>

                <!-- Progress Stepper Integrated -->
                <div class="mt-16 pt-10 border-t border-zinc-100 relative z-10">
                    <div class="flex items-center justify-between mb-10">
                        <h5 class="font-inter text-[10px] text-zinc-400 uppercase tracking-[0.15em] font-bold">État d'avancement de votre dossier</h5>
                        <span class="font-montserrat text-sm text-[#af101a] font-bold">20% Complété</span>
                    </div>
                    <div class="flex items-center justify-between px-2">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#006444] text-white flex items-center justify-center shadow-lg shadow-[#006444]/20 ring-4 ring-[#006444]/5">
                                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check</span>
                            </div>
                            <span class="font-inter text-[10px] text-zinc-900 font-bold text-center whitespace-nowrap">Compte Créé</span>
                        </div>
                        <div class="step-line bg-[#006444]/30"></div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-full border-2 border-[#af101a] bg-white flex items-center justify-center shadow-xl shadow-[#af101a]/10 animate-pulse-subtle">
                                <span class="material-symbols-outlined text-[#af101a] text-xl">pending_actions</span>
                            </div>
                            <span class="font-inter text-[10px] text-[#af101a] font-black text-center whitespace-nowrap uppercase tracking-tighter">Pré-inscription</span>
                        </div>
                        <div class="step-line bg-zinc-100"></div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center gap-3 opacity-30">
                            <div class="w-10 h-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-zinc-400 text-lg">how_to_reg</span>
                            </div>
                            <span class="font-inter text-[10px] text-zinc-400 font-medium text-center whitespace-nowrap">Inscription</span>
                        </div>
                        <div class="step-line bg-zinc-100"></div>

                        <!-- Step 4 -->
                        <div class="flex flex-col items-center gap-3 opacity-30">
                            <div class="w-10 h-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-zinc-400 text-lg">payments</span>
                            </div>
                            <span class="font-inter text-[10px] text-zinc-400 font-medium text-center whitespace-nowrap">Paiement</span>
                        </div>
                        <div class="step-line bg-zinc-100"></div>

                        <!-- Step 5 -->
                        <div class="flex flex-col items-center gap-3 opacity-30">
                            <div class="w-10 h-10 rounded-full bg-zinc-50 border border-zinc-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-zinc-400 text-lg">laptop_mac</span>
                            </div>
                            <span class="font-inter text-[10px] text-zinc-400 font-medium text-center whitespace-nowrap">Ordinateur</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
