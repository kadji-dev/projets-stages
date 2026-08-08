@extends('layouts.staff')

@section('title', 'Campus360 Admin | Filières')

@section('content')

<script>
function fieldsPage() {
    return {
        modalOpen: false,
        mode: 'create',
        form: { id: null, cursus_id: '', code: '', label: '' },

        openCreate() {
            this.mode = 'create';
            this.form = { id: null, cursus_id: '', code: '', label: '' };
            this.modalOpen = true;
        },

        openEdit(el) {
            this.mode = 'edit';
            this.form = {
                id: el.dataset.id,
                cursus_id: el.dataset.cursusId,
                code: el.dataset.code,
                label: el.dataset.label
            };
            this.modalOpen = true;
        }
    };
}
</script>

<div class="space-y-8" x-data="fieldsPage()">
    <x-staff.flash />

    <x-staff.page-header
        title="Filières"
        subtitle="Domaines d'enseignement, rattachés à un cursus."
    >
        <x-slot:actions>
            <x-staff.add-button @click="openCreate()">Nouvelle filière</x-staff.add-button>
        </x-slot:actions>
    </x-staff.page-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-staff.stat-card label="Total Filières" :value="$stats['fields']" />
        <x-staff.stat-card label="Total Spécialités" :value="$stats['specialities']" />
        <x-staff.stat-card label="Total Cursus" :value="$stats['cursuses']" />
    </div>

    @if ($cursuses->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold px-5 py-4 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">info</span>
            Crée d'abord un cursus (BTS, Licence...) avant de pouvoir ajouter une filière.
        </div>
    @endif

    <x-staff.table-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Code</th>
                    <th class="px-6 py-4">Libellé</th>
                    <th class="px-6 py-4">Cursus</th>
                    <th class="px-6 py-4">Spécialités</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($fields as $field)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $field->code }}</td>
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $field->label }}</td>
                        <td class="px-6 py-5"><x-staff.status-badge status="accent">{{ $field->cursus->code }}</x-staff.status-badge></td>
                        <td class="px-6 py-5 text-zinc-600">{{ $field->specialities_count }}</td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <x-staff.edit-button
                                    data-id="{{ $field->id }}"
                                    data-cursus-id="{{ $field->cursus_id }}"
                                    data-code="{{ $field->code }}"
                                    data-label="{{ $field->label }}"
                                    @click="openEdit($el)"
                                />
                                <x-staff.delete-button
                                    :action="route('academic.fields.destroy', $field)"
                                    title="Supprimer cette filière ?"
                                    :message="'La filière « '.$field->label.' » ainsi que ses spécialités et niveaux liés seront supprimés.'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-400">Aucune filière enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <x-staff.modal title="Filière">
        <form
            method="POST"
            :action="mode === 'edit' ? '{{ url('staff/academic/fields') }}/' + form.id : '{{ route('academic.fields.store') }}'"
            class="space-y-4"
        >
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Cursus</label>
                <select x-model="form.cursus_id" name="cursus_id" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                    <option value="">Sélectionner...</option>
                    @foreach ($cursuses as $cursus)
                        <option value="{{ $cursus->id }}">{{ $cursus->label }} ({{ $cursus->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Code</label>
                <input x-model="form.code" name="code" type="text" placeholder="Ex: GI" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Libellé</label>
                <input x-model="form.label" name="label" type="text" placeholder="Ex: Génie Informatique" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Enregistrer</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
