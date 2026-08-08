@extends('layouts.student')

@section('title', 'Campus360 | Pré-inscription confirmée')
@section('page-title', 'Pré-inscription')

@section('content')
<div class="flex-1 p-4 md:p-12 max-w-3xl mx-auto w-full">
    <div class="p-8 md:p-12 bg-white rounded-3xl border border-zinc-100 premium-shadow space-y-8">

        <div class="text-center space-y-4">
            <div class="w-20 h-20 mx-auto rounded-full bg-[#006444]/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[#006444] text-4xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <div class="space-y-2">
                <h1 class="text-2xl md:text-3xl font-bold text-zinc-900 font-montserrat">Pré-inscription enregistrée !</h1>
                <p class="text-zinc-500 font-inter max-w-lg mx-auto">
                    Merci {{ $preEnrollment->prenom }}, ton dossier a bien été reçu. Télécharge ton récapitulatif PDF
                    ci-dessous et présente-le, imprimé, lors de ton passage au campus pour finaliser ton inscription.
                </p>
            </div>
        </div>

        <!-- Récapitulatif du dossier -->
        <div class="rounded-2xl border border-zinc-100 p-6 flex flex-col sm:flex-row gap-6 items-center sm:items-start">
            <div class="w-24 h-24 rounded-2xl bg-zinc-50 border border-zinc-200 shrink-0 overflow-hidden flex items-center justify-center">
                @if ($photoData)
                    <img src="{{ $photoData }}" alt="Photo" class="w-full h-full object-cover">
                @else
                    <span class="material-symbols-outlined text-zinc-300 text-3xl">person</span>
                @endif
            </div>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm w-full">
                <div><span class="text-zinc-400">Nom &amp; Prénom</span><p class="font-bold text-zinc-900">{{ $preEnrollment->nom }} {{ $preEnrollment->prenom }}</p></div>
                <div><span class="text-zinc-400">Date de naissance</span><p class="font-bold text-zinc-900">{{ $preEnrollment->date_naissance->format('d/m/Y') }}</p></div>
                <div><span class="text-zinc-400">Cursus visé</span><p class="font-bold text-zinc-900">{{ $preEnrollment->cursus->label }}</p></div>
                <div><span class="text-zinc-400">Filière</span><p class="font-bold text-zinc-900">{{ $preEnrollment->field->label }}</p></div>
                <div><span class="text-zinc-400">Spécialité</span><p class="font-bold text-zinc-900">{{ $preEnrollment->speciality->label ?? 'Tronc commun' }}</p></div>
                <div><span class="text-zinc-400">Niveau visé</span><p class="font-bold text-zinc-900">{{ $preEnrollment->level->label }}</p></div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4 pt-2">
            <a href="{{ route('pre-enrollments.pdf', $preEnrollment) }}" class="bg-[#af101a] text-white px-8 py-4 rounded-xl font-bold inline-flex items-center justify-center gap-3 shadow-xl shadow-[#af101a]/20 hover:scale-105 transition-all">
                <span class="material-symbols-outlined">download</span>
                Télécharger le récapitulatif (PDF)
            </a>
            <a href="{{ route('student-dashboard.dashboard') }}" class="border border-zinc-200 text-zinc-700 px-8 py-4 rounded-xl font-bold inline-flex items-center justify-center gap-3 hover:bg-zinc-50 transition-all">
                Retour au tableau de bord
            </a>
        </div>

    </div>
</div>
@endsection
