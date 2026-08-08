@extends('layouts.staff')

@section('title', 'Campus360 Admin | Cursus')

@section('content')
<div
    class="space-y-8"
    x-data="{
        modalOpen: false,
        mode: 'create',
        form: { id: null, code: '', label: '', duration_years: 1 },
        openCreate() {
            this.mode = 'create';
            this.form = { id: null, code: '', label: '', duration_years: 1 };
            this.modalOpen = true;
        },
        openEdit(el) {
            this.mode = 'edit';
            this.form = {
                id: el.dataset.id,
                code: el.dataset.code,
                label: el.dataset.label,
                duration_years: el.dataset.durationYears
            };
            this.modalOpen = true;
        }
    }"
>
    <x-staff.flash />

    <x-staff.page-header
        title="Cursus"
        subtitle="Cycles d'études disponibles (BTS, Licence, Master...)."
    >
        <x-slot:actions>
            <button type="button" @click="openCreate()" class="bg-[#af101a] text-white px-6 py-3 rounded-xl font-bold text-sm inline-flex items-center gap-2 shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-lg">add</span>
                Nouveau cursus
            </button>
        </x-slot:actions>
    </x-staff.page-header>

    <x-staff.table-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Code</th>
                    <th class="px-6 py-4">Libellé</th>
                    <th class="px-6 py-4">Durée</th>
                    <th class="px-6 py-4">Filières</th>
                    <th class="px-6 py-4">Niveaux</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($cursuses as $cursus)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $cursus->code }}</td>
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $cursus->label }}</td>
                        <td class="px-6 py-5 text-zinc-600">{{ $cursus->duration_years }} an(s)</td>
                        <td class="px-6 py-5 text-zinc-600">{{ $cursus->fields_count }}</td>
                        <td class="px-6 py-5 text-zinc-600">{{ $cursus->levels_count }}</td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <x-staff.edit-button
                                    data-id="{{ $cursus->id }}"
                                    data-code="{{ $cursus->code }}"
                                    data-label="{{ $cursus->label }}"
                                    data-duration-years="{{ $cursus->duration_years }}"
                                    @click="openEdit($el)"
                                />
                                <x-staff.delete-button
                                    :action="route('academic.cursuses.destroy', $cursus)"
                                    title="Supprimer ce cursus ?"
                                    :message="'Le cursus « '.$cursus->label.' » ainsi que ses filières et niveaux liés seront supprimés.'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-400">Aucun cursus enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <x-staff.modal title="Cursus">
        <form
            method="POST"
            :action="mode === 'edit' ? '{{ url('staff/academic/cursuses') }}/' + form.id : '{{ route('academic.cursuses.store') }}'"
            class="space-y-4"
        >
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Code</label>
                    <input x-model="form.code" name="code" type="text" placeholder="Ex: BTS" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Durée (années)</label>
                    <input x-model="form.duration_years" name="duration_years" type="number" min="1" max="8" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Libellé</label>
                <input x-model="form.label" name="label" type="text" placeholder="Ex: Brevet de Technicien Supérieur" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Enregistrer</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
