<div class="bg-white rounded-2xl border border-zinc-100 p-6 premium-shadow flex flex-col gap-4 hover:-translate-y-1 hover:shadow-xl transition-all">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-[#af101a]/10 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[#af101a]">account_tree</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $code }}</span>
                <h4 class="font-montserrat font-bold text-zinc-900 leading-tight">{{ $libelle }}</h4>
            </div>
        </div>
        <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-[#006444]/10 text-[#006444] shrink-0">{{ $diplome }}</span>
    </div>

    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-zinc-100">
        <div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Semestres</p>
            <p class="font-montserrat font-bold text-zinc-900 text-lg">{{ $semestres }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Spécialités</p>
            <p class="font-montserrat font-bold text-zinc-900 text-lg">{{ $specialites }}</p>
        </div>
    </div>

    <div class="flex gap-2 pt-2">
        <a href="{{ $editRoute }}" class="flex-1 text-center text-xs font-bold py-2.5 rounded-lg border border-zinc-200 text-zinc-700 hover:border-[#af101a] hover:text-[#af101a] transition-colors">Modifier</a>
        <a href="{{ $deleteRoute }}" class="flex-1 text-center text-xs font-bold py-2.5 rounded-lg border border-zinc-200 text-zinc-400 hover:border-red-300 hover:text-red-500 transition-colors">Supprimer</a>
    </div>
</div>
