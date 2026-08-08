<header class="h-20 flex items-center justify-between gap-6 px-4 md:px-12 sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-zinc-100">
    <div class="flex items-center gap-4 flex-1">
        <button class="md:hidden text-zinc-900 cursor-pointer">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <label class="hidden md:flex items-center gap-3 w-full max-w-md bg-zinc-50 border border-zinc-100 rounded-2xl px-4 py-3">
            <span class="material-symbols-outlined text-zinc-400 text-lg">search</span>
            <input type="text" placeholder="Rechercher..." class="bg-transparent outline-none text-sm font-inter placeholder:text-zinc-400 w-full">
        </label>
    </div>

    <div class="flex items-center gap-6">
        <button class="relative text-zinc-500 hover:text-[#af101a] transition-colors cursor-pointer" type="button">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-[#af101a] ring-2 ring-white"></span>
        </button>

        <div class="flex items-center gap-3 relative" id="admin-profile-trigger">
            <div class="hidden md:flex flex-col items-end cursor-pointer">
                <span class="font-inter text-sm text-zinc-900 font-bold">{{ auth()->user()->name ?? 'Admin Principal' }}</span>
                <span class="font-inter text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Direction Campus360</span>
            </div>
            <div class="w-11 h-11 rounded-full bg-[#af101a] flex items-center justify-center text-white shadow-lg shadow-[#af101a]/20 cursor-pointer">
                <span class="material-symbols-outlined">person</span>
            </div>

            <div id="admin-profile-menu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-2xl border border-zinc-100 py-2 z-50">
                <button class="w-full flex items-center gap-3 px-4 py-2 text-sm font-montserrat text-zinc-700 hover:bg-zinc-50 hover:text-[#af101a] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">settings</span>
                    <span>Paramètres</span>
                </button>
                <div class="h-px bg-zinc-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm font-montserrat text-zinc-700 hover:bg-zinc-50 hover:text-[#af101a] transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
(function () {
    const trigger = document.getElementById('admin-profile-trigger');
    const menu = document.getElementById('admin-profile-menu');
    if (trigger && menu) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target)) menu.classList.add('hidden');
        });
    }
})();
</script>
