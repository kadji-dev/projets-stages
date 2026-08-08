<header class="h-20 flex items-center justify-between px-4 md:px-12 sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-zinc-100">
    <div class="flex items-center gap-4">
        <button class="md:hidden text-zinc-900 cursor-pointer">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <h2 class="font-montserrat text-xl font-bold text-zinc-900">@yield('page-title', 'Tableau de bord')</h2>
    </div>
    <div class="flex items-center gap-4 relative" id="profile-dropdown-trigger">
        <div class="hidden md:flex flex-col items-end mr-1 cursor-pointer">
            <span class="font-inter text-sm text-zinc-900 font-bold">{{ auth()->user()->name ?? 'Jean-Marc Koffi' }}</span>
            <span class="font-inter text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">ID: {{ auth()->user()->matricule ?? 'ESC-2024-089' }}</span>
        </div>
        <div class="w-11 h-11 rounded-full ring-2 ring-[#af101a]/10 overflow-hidden shadow-inner cursor-pointer">
            <img alt="Profile" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA89KWPbqphM_eOGB_6n0cloB--LEvzBvOTiTfx9gLbtONYhacHtboe6uZgZZ9oU3ICkdM5q1GqzNFEtuFi3XqzU2NwToMkliwY7sgmbrhyNI0mBGgTm8LIZRFdlu7E_87sdKmaJW-0H9--Xq3hTR2z19bC79mrhIQvMxOD7_vVBlgUFzXAW1sPq24C82J25_NNWPaYTmbpDrSyz-vHXClXD7SPNNdrKLuMlavYTj0Fa62BdNC9AGqvfTOKO5RJQ38nxg74VuQExQ">
        </div>

        <div id="profile-menu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-2xl border border-zinc-100 py-2 z-50">
            <button class="w-full flex items-center gap-3 px-4 py-2 text-sm font-montserrat text-zinc-700 hover:bg-zinc-50 hover:text-[#af101a] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-lg">info</span>
                <span>À propos</span>
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
</header>

<script>
(function () {
    const trigger = document.getElementById('profile-dropdown-trigger');
    const menu = document.getElementById('profile-menu');
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
