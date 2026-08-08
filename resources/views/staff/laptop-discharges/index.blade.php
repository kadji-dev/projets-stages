@extends('layouts.staff')

@section('title', 'Campus360 Admin | Remise des ordinateurs')

@section('content')

@php
    // Données de démonstration en attendant le contrôleur (mêmes noms de
    // variables qu'utilisera la logique réelle, pour un branchement direct).
    $eligible = $eligible ?? collect([
        (object) [
            'id' => 1,
            'matricule' => 'ESC-2026-0001',
            'total_paid' => 150000,
            'user' => (object) ['name' => 'Jean-Marc Koffi', 'email' => 'jean.koffi@Campus360.cm'],
            'cursus' => (object) ['label' => 'Licence'],
            'field' => (object) ['label' => 'Génie Informatique'],
        ],
    ]);

    $availableLaptops = $availableLaptops ?? collect([
        (object) ['id' => 1, 'reference' => 'PC-0232', 'brand' => 'HP', 'model' => 'ProBook 440'],
        (object) ['id' => 2, 'reference' => 'PC-0240', 'brand' => 'Dell', 'model' => 'Latitude 5420'],
    ]);
@endphp

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laptopDischargePage', () => ({
        modalOpen: false,
        selectedEnrollmentId: '',
        selectedEnrollmentName: '',
        selectedLaptopId: '',

        openModal(enrollmentId, name) {
            this.selectedEnrollmentId = enrollmentId;
            this.selectedEnrollmentName = name;
            this.selectedLaptopId = '';
            this.modalOpen = true;
        },
    }));
});
</script>

<div class="space-y-8" x-data="laptopDischargePage()">
    <x-staff.flash />

    <x-staff.page-header
        title="Remise des ordinateurs"
        subtitle="Étudiants ayant atteint 150 000 XAF et prêts à retirer leur PC."
    />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-staff.stat-card label="Étudiants éligibles" :value="$eligible->count()" value-class="text-[#af101a]" />
        <x-staff.stat-card label="Ordinateurs disponibles en stock" :value="$availableLaptops->count()" value-class="text-[#006444]" />
    </div>

    <x-staff.table-card title="Éligibles au retrait" icon="verified">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Étudiant</th>
                    <th class="px-6 py-4">Matricule</th>
                    <th class="px-6 py-4">Filière</th>
                    <th class="px-6 py-4">Total versé</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($eligible as $enrollment)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5">
                            <p class="font-montserrat font-bold text-zinc-900">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-zinc-400">{{ $enrollment->user->email }}</p>
                        </td>
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $enrollment->matricule }}</td>
                        <td class="px-6 py-5 text-zinc-600">{{ $enrollment->cursus->label }} — {{ $enrollment->field->label }}</td>
                        <td class="px-6 py-5 font-bold text-[#006444]">{{ number_format($enrollment->total_paid, 0, ',', ' ') }} XAF</td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="openModal({{ $enrollment->id }}, {{ json_encode($enrollment->user->name) }})"
                                    class="bg-[#af101a] text-white px-4 py-2 rounded-lg text-xs font-bold inline-flex items-center gap-1 hover:scale-105 transition-all cursor-pointer"
                                >
                                    <span class="material-symbols-outlined text-sm">laptop_mac</span>
                                    Valider la remise
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-400">Aucun étudiant éligible pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <!-- Modale validation remise -->
    <x-staff.modal title="Remise de l'ordinateur">
        <form method="POST" action="#" class="space-y-4">
            @csrf

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Étudiant</label>
                <div class="p-3 rounded-xl bg-[#af101a]/5 border border-[#af101a]/20 font-bold text-zinc-900" x-text="selectedEnrollmentName"></div>
                <input type="hidden" name="enrollment_id" x-model="selectedEnrollmentId">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Ordinateur à remettre</label>
                <select x-model="selectedLaptopId" name="laptop_id" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                    <option value="">Sélectionner un poste disponible...</option>
                    @foreach ($availableLaptops as $laptop)
                        <option value="{{ $laptop->id }}">{{ $laptop->reference }} — {{ $laptop->brand }} {{ $laptop->model }}</option>
                    @endforeach
                </select>
                @if ($availableLaptops->isEmpty())
                    <p class="text-xs text-red-500">Aucun ordinateur disponible en stock actuellement.</p>
                @endif
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700 flex items-start gap-2">
                <span class="material-symbols-outlined text-base">info</span>
                Cette action décrémente automatiquement le stock et marque le poste comme « Attribué ».
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Confirmer la remise</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
