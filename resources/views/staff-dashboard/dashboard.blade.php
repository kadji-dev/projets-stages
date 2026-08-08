@extends('layouts.staff')

@section('title', 'Campus360 Admin | Tableau de bord')

@section('content')
<div class="space-y-10">

    <x-staff.flash />

    <x-staff.page-header
        title="Tableau de bord"
        subtitle="Vue d'ensemble de la structure académique."
    />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-staff.stat-card label="Années Académiques" :value="$stats['years'] ?? 0" />
        <x-staff.stat-card label="Filières" :value="$stats['fields'] ?? 0" />
        <x-staff.stat-card label="Cursus" :value="$stats['cursuses'] ?? 0" value-class="text-[#af101a]" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Année en cours -->
        <div class="bg-white rounded-2xl border border-zinc-100 p-6 premium-shadow flex flex-col gap-4">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Année en cours</p>
            @if ($currentYear ?? null)
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#006444]"></span>
                    <span class="font-montserrat text-2xl font-extrabold text-zinc-900">{{ $currentYear->label }}</span>
                    <x-staff.status-badge status="success">En cours</x-staff.status-badge>
                </div>
                <p class="text-sm text-zinc-500">{{ $currentYear->period }}</p>
            @else
                <p class="text-sm text-zinc-400">Aucune année académique n'est définie comme "en cours".</p>
            @endif
            <a href="{{ route('academic.years') }}" class="text-sm font-bold text-[#af101a] hover:underline inline-flex items-center gap-1 mt-2">
                Gérer les années
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>

        <!-- Accès rapides -->
        <div class="bg-white rounded-2xl border border-zinc-100 p-6 premium-shadow">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-4">Accès rapides</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['label' => 'Cursus', 'route' => 'academic.cursuses'],
                    ['label' => 'Filières', 'route' => 'academic.fields'],
                    ['label' => 'Spécialités', 'route' => 'academic.specialities'],
                    ['label' => 'Niveaux', 'route' => 'academic.levels'],
                    ['label' => 'Admissions', 'route' => 'staff-dashboard.admissions'],
                    ['label' => 'Années Académiques', 'route' => 'academic.years'],
                ] as $quick)
                    <a href="{{ route($quick['route']) }}" class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-zinc-100 hover:border-[#af101a]/30 hover:bg-[#af101a]/5 transition-colors text-sm font-semibold text-zinc-700">
                        {{ $quick['label'] }}
                        <span class="material-symbols-outlined text-base text-[#af101a]">arrow_forward</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
