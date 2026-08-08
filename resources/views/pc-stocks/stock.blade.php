@extends('layouts.staff')

@section('title', 'Campus360 Admin | Stock PC')

@section('content')

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laptopStockPage', () => ({
        modalOpen: false,
        mode: 'create',
        form: { id: null, reference: '', brand: '', model: '', serial_number: '', status: 'disponible', notes: '' },

        openCreate() {
            this.mode = 'create';
            this.form = { id: null, reference: '', brand: '', model: '', serial_number: '', status: 'disponible', notes: '' };
            this.modalOpen = true;
        },

        openEdit(laptop) {
            this.mode = 'edit';
            this.form = { ...laptop };
            this.modalOpen = true;
        },
    }));
});
</script>

<div class="space-y-8" x-data="laptopStockPage()">
    <x-staff.flash />

    <x-staff.page-header
        title="Stock PC"
        subtitle="Gestion du parc informatique attribué aux étudiants."
    >
        <x-slot:actions>
            <x-staff.add-button @click="openCreate()">Ajouter un poste</x-staff.add-button>
        </x-slot:actions>
    </x-staff.page-header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-staff.stat-card label="Total parc" :value="$stats['total']" />
        <x-staff.stat-card label="Disponibles" :value="$stats['disponible']" value-class="text-[#006444]" />
        <x-staff.stat-card label="Attribués" :value="$stats['attribue']" />
        <x-staff.stat-card label="En maintenance" :value="$stats['maintenance']" value-class="text-[#af101a]" />
    </div>

    <x-staff.table-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Référence</th>
                    <th class="px-6 py-4">Modèle</th>
                    <th class="px-6 py-4">N° de série</th>
                    <th class="px-6 py-4">État</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse ($laptops as $laptop)
                    <tr class="hover:bg-zinc-50/60 transition-colors">
                        <td class="px-6 py-5 font-mono text-xs text-zinc-500">{{ $laptop->reference }}</td>
                        <td class="px-6 py-5 font-montserrat font-bold text-zinc-900">{{ $laptop->brand }} {{ $laptop->model }}</td>
                        <td class="px-6 py-5 text-zinc-600 font-mono text-xs">{{ $laptop->serial_number ?? '—' }}</td>
                        <td class="px-6 py-5">
                            <x-staff.status-badge :status="$laptop->badge_status">{{ $laptop->status_label }}</x-staff.status-badge>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-end gap-2">
                                <x-staff.edit-button
                                    @click="openEdit({
                                        id: {{ $laptop->id }},
                                        reference: {{ json_encode($laptop->reference) }},
                                        brand: {{ json_encode($laptop->brand) }},
                                        model: {{ json_encode($laptop->model) }},
                                        serial_number: {{ json_encode($laptop->serial_number) }},
                                        status: {{ json_encode($laptop->status) }},
                                        notes: {{ json_encode($laptop->notes) }}
                                    })"
                                />
                                <x-staff.delete-button
                                    :action="route('staff-dashboard.pc-stock.destroy', $laptop)"
                                    title="Supprimer ce poste ?"
                                    :message="'Le poste « '.$laptop->reference.' » sera définitivement retiré du parc.'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-400">Aucun poste enregistré dans le parc.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-staff.table-card>

    <x-staff.modal title="Poste informatique">
        <form
            method="POST"
           :action="mode === 'edit'
? '{{ url('staff/pc-stock') }}/' + form.id
: '{{ route('staff-dashboard.pc-stock.store') }}'"
            class="space-y-4"
        >
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Référence</label>
                <input x-model="form.reference" name="reference" type="text" placeholder="Ex: PC-0231" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Marque</label>
                    <input x-model="form.brand" name="brand" type="text" placeholder="Ex: Dell" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-zinc-600">Modèle</label>
                    <input x-model="form.model" name="model" type="text" placeholder="Ex: Latitude 5480" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Numéro de série (optionnel)</label>
                <input x-model="form.serial_number" name="serial_number" type="text" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">État</label>
                <select x-model="form.status" name="status" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20">
                    <option value="disponible">Disponible</option>
                    <option value="attribue">Attribué</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-zinc-600">Notes (optionnel)</label>
                <textarea x-model="form.notes" name="notes" class="w-full p-3 rounded-xl bg-zinc-50 border border-zinc-200 focus:ring-2 focus:ring-[#af101a]/20 min-h-[80px]"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#af101a] text-white text-sm font-bold shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all">Enregistrer</button>
            </div>
        </form>
    </x-staff.modal>

</div>
@endsection
