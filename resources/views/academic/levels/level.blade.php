@extends('layouts.staff')

@section('title', 'Campus360 Admin | Niveaux')

@section('content')

<script>
function levelsPage(defaultFieldId) {
    return {
        modalOpen: false,
        mode: 'create',
        form: { id: null, field_id: defaultFieldId, speciality_id: '', code: '', label: '', order: 1 },

        openCreate() {
            this.mode = 'create';
            this.form = { id: null, field_id: defaultFieldId, speciality_id: '', code: '', label: '', order: 1 };
            this.modalOpen = true;
        },

        openEdit(el) {
            this.mode = 'edit';
            this.form = {
                id: el.dataset.id,
                field_id: el.dataset.fieldId,
                speciality_id: el.dataset.specialityId || '',
                code: el.dataset.code,
                label: el.dataset.label,
                order: el.dataset.order
            };
            this.modalOpen = true;
        }
    };
}
</script>

<div class="space-y-8" x-data="levelsPage('{{ $selectedFieldId }}')">
    <x-staff.flash />

    <x-staff.page-header
        title="Niveaux"
        subtitle="Ajouter un niveau d'études."
    >
        <x-slot:actions>
            <x-staff.add-button @click="openCreate()">Nouveau niveau</x-staff.add-button>
        </x-slot:actions>
    </x-staff.page-header>

    @if ($fields->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold px-5 py-4 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">info</span>
            Crée d'abord une filière avant de pouvoir ajouter un niveau.
        </div>
    @endif

    <form method="GET" action="{{ route('academic.levels') }}" class="flex gap-4 flex-wrap">
        <select name="field_id" onchange="this.form.submit()" class="bg-white border border-zinc-200 rounded-xl px-4 py-3 text-sm font-semibold text-zinc-700 shadow-sm">
            <option value="">Toutes les filières</option>
            @foreach ($fields as $field)
                <option value="{{ $field->id }}" @selected($selectedFieldId == $field->id)>{{ $field->label }} — {{ $field->cursus->code }}</option>
            @endforeach
        </select>
        <select name="speciality_id" onchange="this.form.submit()" class="bg-white border border-zinc-200 rounded-xl px-4 py-3 text-sm font-semibold text-zinc-700 shadow-sm">
            <option value="">Toutes les spécialités</option>
            @foreach ($specialities as $sp)
                <option value="{{ $sp->id }}" @selected($selectedSpecialityId == $sp->id)>{{ $sp->label }}</option>
            @endforeach
        </select>
    </form>

    <x-staff.table-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Code</th>
                    <th class="px-6 py-4">Libellé</th>
                    <th class="px-6 py-4">Filière</th>
                    <th class="px-6 py-4">Spécialité</th>
                    <th class="px-6 py-4">Ordre</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($levels as $level)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $level->code }}</td>
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $level->label }}</td>
                        <td class="px-6 py-5"><x-staff.status-badge status="accent">{{ $level->field->label }}</x-staff.status-badge></td>
                        <td class="px-6 py-5">
                            @if ($level->speciality)
                                <span class="text-zinc-600 text-sm">{{ $level->speciality->label }}</span>
                            @else
                                <span class="text-zinc-400 italic text-xs">Tronc commun</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-zinc-600">{{ $level->order }}</td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <x-staff.edit-button
                                    data-id="{{ $level->id }}"
                                    data-field-id="{{ $level->field_id }}"
                                    data-speciality-id="{{ $level->speciality_id }}"
                                    data-code="{{ $level->code }}"
                                    data-label="{{ $level->label }}"
                                    data-order="{{ $level->order }}"
                                    @click="openEdit($el)"
                                />
                                <x-staff.delete-button
                                    :action="route('academic.levels.destroy', $level)"
                                    title="Supprimer ce niveau ?"
                                    :message="'Le niveau « '.$level->label.' » sera définitivement supprimé.'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-400">Aucun niveau enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <x-staff.modal title="Niveau">
        <form
            method="POST"
            :action="mode === 'edit' ? '{{ url('staff/academic/levels') }}/' + form.id : '{{ route('academic.levels.store') }}'"
            class="space-y-4"
        >
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Filière <span class="text-[#af101a]">*</span></label>
                <select x-model="form.field_id" name="field_id" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                    <option value="">Sélectionner...</option>
                    @foreach ($fields as $field)
                        <option value="{{ $field->id }}">{{ $field->label }} — {{ $field->cursus->code }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Spécialité (optionnel — laisser vide pour tronc commun)</label>
                <select x-model="form.speciality_id" name="speciality_id" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                    <option value="">Sélectionner...</option>
                    @foreach ($specialities as $sp)
                        <option value="{{ $sp->id }}">{{ $sp->label }} ({{ $sp->field->label }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Code</label>
                    <input x-model="form.code" name="code" type="text" placeholder="Ex : L1" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Ordre</label>
                    <input x-model="form.order" name="order" type="number" min="1" max="20" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Libellé</label>
                <input x-model="form.label" name="label" type="text" placeholder="Ex : Niveau 1" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Enregistrer</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
