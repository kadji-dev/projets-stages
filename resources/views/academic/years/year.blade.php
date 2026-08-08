@extends('layouts.staff')

@section('title', 'Campus360 Admin | Années Académiques')

@section('content')

<script>
function academicYearsPage() {
    return {
        modalOpen: false,
        mode: 'create',
        form: { id: null, label: '', start_date: '', end_date: '', is_current: false },

        openCreate() {
            this.mode = 'create';
            this.form = { id: null, label: '', start_date: '', end_date: '', is_current: false };
            this.modalOpen = true;
        },

        openEdit(el) {
            this.mode = 'edit';
            this.form = {
                id: el.dataset.id,
                label: el.dataset.label,
                start_date: el.dataset.startDate,
                end_date: el.dataset.endDate,
                is_current: el.dataset.isCurrent === '1'
            };
            this.modalOpen = true;
        }
    };
}
</script>

<div class="space-y-8" x-data="academicYearsPage()">
    <x-staff.flash />

    <x-staff.page-header
        title="Gestion des Années Académiques"
        subtitle="Configurez et gérez les cycles scolaires de l'établissement."
    >
        <x-slot:actions>
            <x-staff.add-button @click="openCreate()">Nouvelle année</x-staff.add-button>
        </x-slot:actions>
    </x-staff.page-header>

    <x-staff.table-card title="Registre des cycles" icon="history">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Libellé</th>
                    <th class="px-6 py-4">Période académique</th>
                    <th class="px-6 py-4">En cours</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($years as $year)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $year->label }}</td>
                        <td class="px-6 py-5 text-zinc-700">{{ $year->period }}</td>
                        <td class="px-6 py-5">
                            <x-staff.status-badge :status="$year->is_current ? 'success' : 'default'">
                                {{ $year->is_current ? 'En cours' : 'Autre' }}
                            </x-staff.status-badge>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <x-staff.edit-button
                                    data-id="{{ $year->id }}"
                                    data-label="{{ $year->label }}"
                                    data-start-date="{{ $year->start_date ? $year->start_date->format('Y-m-d') : '' }}"
                                    data-end-date="{{ $year->end_date ? $year->end_date->format('Y-m-d') : '' }}"
                                    data-is-current="{{ $year->is_current ? '1' : '0' }}"
                                    @click="openEdit($el)"
                                />

                                <x-staff.delete-button
                                    :action="route('academic.years.destroy', $year)"
                                    title="Supprimer cette année académique ?"
                                    :message="'Le cycle « '.$year->label.' » sera définitivement supprimé.'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-zinc-400">Aucune année académique enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <x-staff.modal title="Année académique">
        <form
            method="POST"
            :action="mode === 'edit' ? '{{ url('staff/academic/years') }}/' + form.id : '{{ route('academic.years.store') }}'"
            class="space-y-4"
        >
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Libellé</label>
                <input x-model="form.label" name="label" type="text" placeholder="Ex: 2025-2026" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Début</label>
                    <input x-model="form.start_date" name="start_date" type="date" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Fin</label>
                    <input x-model="form.end_date" name="end_date" type="date" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input x-model="form.is_current" name="is_current" type="checkbox" value="1" class="w-4 h-4 rounded border-zinc-300 text-[#af101a] focus:ring-[#af101a]/30">
                <span class="text-sm text-zinc-700">Définir comme année en cours</span>
            </label>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Enregistrer</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
