@extends('layouts.staff')

@section('title', 'Admissions | Campus360 Staff')
@section('page-title', 'Admissions')

@section('content')
<div x-data="{
    showAddModal: false,
    showEditModal: false,
    editStudent: { id: null, name: '', email: '', phone: '', program: '', level: '', status: '', matricule: '' }
}" class="p-4 md:p-12 space-y-8">

    {{-- KPIs DYNAMIQUES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-zinc-400 uppercase tracking-wider">TOTAL CANDIDATURES</p>
                <h3 class="text-2xl font-montserrat font-bold text-zinc-900 mt-1">{{ $totalApplicants }}</h3>
            </div>
            <div class="w-12 h-12 bg-zinc-100 rounded-xl flex items-center justify-center text-zinc-700">
                <span class="material-symbols-outlined">folder_shared</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-amber-600 uppercase tracking-wider">EN ATTENTE / EN COURS</p>
                <h3 class="text-2xl font-montserrat font-bold text-amber-600 mt-1">{{ $pendingCount }}</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-emerald-600 uppercase tracking-wider">INSCRITS & MATRICULÉS</p>
                <h3 class="text-2xl font-montserrat font-bold text-emerald-600 mt-1">{{ $enrolledCount }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined">how_to_reg</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-inter font-semibold text-indigo-600 uppercase tracking-wider">ÉLIGIBLES LAPTOP</p>
                <h3 class="text-2xl font-montserrat font-bold text-indigo-600 mt-1">{{ $laptopEligibleCount }}</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined">laptop_mac</span>
            </div>
        </div>
    </div>

    {{-- BARRE DE FILTRES RECHERCHE --}}
    <form method="GET" action="{{ route('staff-dashboard.admissions') }}" class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="relative w-full md:w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, email, matricule..." class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-[#af101a]">
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-inter text-zinc-700">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Inscrit</option>
                <option value="laptop_eligible" {{ request('status') == 'laptop_eligible' ? 'selected' : '' }}>Éligible Laptop</option>
            </select>

            <select name="program" onchange="this.form.submit()" class="px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-inter text-zinc-700">
                <option value="">Toutes les filières</option>
                <option value="Génie Informatique" {{ request('program') == 'Génie Informatique' ? 'selected' : '' }}>Génie Informatique</option>
                <option value="Sciences de Gestion" {{ request('program') == 'Sciences de Gestion' ? 'selected' : '' }}>Sciences de Gestion</option>
            </select>

            <button type="button" @click="showAddModal = true" class="flex items-center gap-2 px-5 py-2.5 bg-[#af101a] text-white rounded-xl text-sm font-semibold hover:bg-[#8f0d15] transition-colors cursor-pointer shadow-md shadow-[#af101a]/20">
                <span class="material-symbols-outlined text-lg">person_add</span>
                <span>Inscrire un étudiant</span>
            </button>
        </div>
    </form>

    {{-- TABLEAU DYNAMIQUE --}}
    <div class="bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50/50 border-b border-zinc-100 font-montserrat text-xs text-zinc-400 uppercase tracking-wider">
                        <th class="p-4 px-6">CANDIDAT</th>
                        <th class="p-4 px-6">FILIÈRE / NIVEAU</th>
                        <th class="p-4 px-6">MATRICULE</th>
                        <th class="p-4 px-6">STATUT ADMISSION</th>
                        <th class="p-4 px-6">STATUT LAPTOP</th>
                        <th class="p-4 px-6">DATE DÉPÔT</th>
                        <th class="p-4 px-6 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 font-inter text-sm text-zinc-700">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="p-4 px-6">
                            <div class="font-bold text-zinc-900">{{ $enrollment->name }}</div>
                            <div class="text-xs text-zinc-400">{{ $enrollment->email }} • {{ $enrollment->phone }}</div>
                        </td>
                        <td class="p-4 px-6">
                            <span class="font-semibold text-zinc-800">{{ $enrollment->program }}</span>
                            <div class="text-xs text-zinc-400">{{ $enrollment->level }}</div>
                        </td>
                        <td class="p-4 px-6">
                            @if($enrollment->matricule)
                                <span class="font-mono text-xs font-bold bg-zinc-100 px-2.5 py-1 rounded text-zinc-900">{{ $enrollment->matricule }}</span>
                            @else
                                <span class="text-xs italic text-zinc-400">Génération à la validation</span>
                            @endif
                        </td>
                        <td class="p-4 px-6">
                            @if($enrollment->status === 'enrolled')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Inscrit
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> En attente
                                </span>
                            @endif
                        </td>
                        <td class="p-4 px-6">
                            @if($enrollment->laptop_eligible)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <span class="material-symbols-outlined text-xs">laptop_mac</span> Éligible
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-500 border border-zinc-200">
                                    <span class="material-symbols-outlined text-xs">block</span> Inéligible (Tranche 1)
                                </span>
                            @endif
                        </td>
                        <td class="p-4 px-6 text-xs text-zinc-500">{{ $enrollment->created_at->format('d M. Y') }}</td>
                        <td class="p-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($enrollment->status !== 'enrolled')
                                <form method="POST" action="{{ route('staff.admissions.approve', $enrollment->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-sm">badge</span>
                                        <span>Valider & Matriculer</span>
                                    </button>
                                </form>
                                @endif

                                <button @click="showEditModal = true; editStudent = { id: {{ $enrollment->id }}, name: '{{ addslashes($enrollment->name) }}', email: '{{ $enrollment->email }}', phone: '{{ $enrollment->phone }}', matricule: '{{ $enrollment->matricule }}' }"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-zinc-200 text-zinc-700 rounded-xl font-medium text-xs hover:bg-zinc-50 transition-colors shadow-sm cursor-pointer">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                    <span>Modifier</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-zinc-400">Aucun étudiant trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-zinc-100">
            {{ $enrollments->links() }}
        </div>
    </div>

    {{-- MODALE AJOUT --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-2xl border border-zinc-100 w-full max-w-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex justify-between items-center bg-zinc-50">
                <h3 class="font-montserrat font-bold text-zinc-900 text-lg">Inscrire un nouvel étudiant</h3>
                <button @click="showAddModal = false" class="text-zinc-400 hover:text-zinc-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" action="{{ route('staff.admissions.store') }}" class="p-6 space-y-4 text-sm">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Nom complet *</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Email *</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Téléphone *</label>
                        <input type="text" name="phone" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Filière *</label>
                        <select name="program" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                            <option value="Génie Informatique">Génie Informatique</option>
                            <option value="Sciences de Gestion">Sciences de Gestion</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-zinc-700 mb-1">Niveau *</label>
                    <select name="level" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                        <option value="BTS 1ère Année">BTS 1ère Année</option>
                        <option value="Licence 1">Licence 1</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded-xl text-zinc-600">Annuler</button>
                    <button type="submit" class="px-5 py-2 bg-[#af101a] text-white rounded-xl font-semibold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODALE ÉDITION --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-zinc-100 w-full max-w-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-zinc-100 flex justify-between items-center bg-zinc-50">
                <h3 class="font-montserrat font-bold text-zinc-900 text-lg">Modifier le dossier</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-zinc-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form method="POST" :action="'/staff/admissions/' + editStudent.id" class="p-6 space-y-4 text-sm">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Nom complet</label>
                        <input type="text" name="name" x-model="editStudent.name" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="editStudent.email" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Téléphone</label>
                        <input type="text" name="phone" x-model="editStudent.phone" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1">Matricule</label>
                        <input type="text" x-model="editStudent.matricule" readonly class="w-full px-3 py-2 bg-zinc-100 text-zinc-500 border border-zinc-200 rounded-xl cursor-not-allowed">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded-xl text-zinc-600">Annuler</button>
                    <button type="submit" class="px-5 py-2 bg-zinc-900 text-white rounded-xl font-semibold">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
