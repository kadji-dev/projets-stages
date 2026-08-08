@extends('layouts.staff')

@section('title', 'Campus360 Admin | Spécialités')

@section('content')

<script>
function specialitiesPage(defaultFieldId) {
    return {
        modalOpen: false,
        mode: 'create',
        form: { id: null, field_id: defaultFieldId, code: '', label: '' },

        openCreate() {
            this.mode = 'create';
            this.form = { id: null, field_id: defaultFieldId, code: '', label: '' };
            this.modalOpen = true;
        },

        openEdit(el) {
            this.mode = 'edit';
            this.form = {
                id: el.dataset.id,
                field_id: el.dataset.fieldId,
                code: el.dataset.code,
                label: el.dataset.label
            };
            this.modalOpen = true;
        }
    };
}
</script>

<div class="space-y-8" x-data="specialitiesPage('{{ $selectedFieldId }}')">
    <x-staff.flash />

    <x-staff.page-header
        title="Spécialités"
        subtitle="Parcours spécifiques rattachés à une filière."
    >
        <x-slot:actions>
            <x-staff.add-button @click="openCreate()">Nouvelle spécialité</x-staff.add-button>
        </x-slot:actions>
    </x-staff.page-header>

    @if ($fields->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold px-5 py-4 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">info</span>
            Crée d'abord une filière avant de pouvoir ajouter une spécialité.
        </div>
    @else
        <form method="GET" action="{{ route('academic.specialities') }}">
            <select name="field_id" onchange="this.form.submit()" class="bg-white border border-zinc-200 rounded-xl px-4 py-3 text-sm font-semibold text-zinc-700 shadow-sm">
                @foreach ($fields as $field)
                    <option value="{{ $field->id }}" @selected($selectedFieldId == $field->id)>{{ $field->label }} — {{ $field->cursus->code }}</option>
                @endforeach
            </select>
        </form>
    @endif

    <x-staff.table-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Code</th>
                    <th class="px-6 py-4">Libellé</th>
                    <th class="px-6 py-4">Filière</th>
                    <th class="px-6 py-4">Cursus</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($specialities as $s)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $s->code }}</td>
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $s->label }}</td>
                        <td class="px-6 py-5 text-zinc-600">{{ $s->field->label }}</td>
                        <td class="px-6 py-5"><x-staff.status-badge status="accent">{{ $s->field->cursus->code }}</x-staff.status-badge></td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <x-staff.edit-button
                                    data-id="{{ $s->id }}"
                                    data-field-id="{{ $s->field_id }}"
                                    data-code="{{ $s->code }}"
                                    data-label="{{ $s->label }}"
                                    @click="openEdit($el)"
                                />
                                <x-staff.delete-button
                                    :action="route('academic.specialities.destroy', $s)"
                                    title="Supprimer cette spécialité ?"
                                    :message="'La spécialité « '.$s->label.' » sera définitivement supprimée.'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-400">Aucune spécialité pour cette filière.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <x-staff.modal title="Spécialité">
        <form
            method="POST"
            :action="mode === 'edit' ? '{{ url('staff/academic/specialities') }}/' + form.id : '{{ route('academic.specialities.store') }}'"
            class="space-y-4"
        >
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Filière</label>
                <select x-model="form.field_id" name="field_id" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                    <option value="">Sélectionner...</option>
                    @foreach ($fields as $field)
                        <option value="{{ $field->id }}">{{ $field->label }} — {{ $field->cursus->code }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Code</label>
                <input x-model="form.code" name="code" type="text" placeholder="Ex: GL" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Libellé</label>
                <input x-model="form.label" name="label" type="text" placeholder="Ex: Génie Logiciel" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Enregistrer</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
