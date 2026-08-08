<aside class="h-screen w-64 fixed left-0 top-0 bg-[#121212] flex flex-col py-8 px-4 z-40 hidden md:flex shadow-2xl">
    <div class="mb-10 px-4">
        <h1 class="font-montserrat text-2xl text-white font-bold tracking-tight">Campus360</h1>
        <p class="font-inter text-xs text-zinc-500 mt-1 uppercase tracking-widest font-semibold">Portail Étudiant</p>
    </div>

    <nav class="flex-1 space-y-1">
        {{-- Tableau de bord --}}
        <a
            href="{{ route('student-dashboard.dashboard') }}"
            class="flex items-center space-x-3 rounded-lg p-3 transition-all {{ request()->routeIs('student-dashboard.*') ? 'bg-zinc-800/50 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/30' }}"
        >
            <span
                class="material-symbols-outlined {{ request()->routeIs('student-dashboard.*') ? 'text-[#af101a]' : '' }}"
                @if(request()->routeIs('student-dashboard.*')) style="font-variation-settings: 'FILL' 1;" @endif
            >dashboard</span>
            <span class="font-inter text-sm font-medium">Tableau de bord</span>
        </a>

        {{-- Pré-inscription --}}
        <a
            href="{{ route('pre-enrollments.index') }}"
            class="flex items-center space-x-3 rounded-lg p-3 transition-all {{ request()->routeIs('pre-enrollments.*') ? 'bg-zinc-800/50 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/30' }}"
        >
            <span
                class="material-symbols-outlined {{ request()->routeIs('pre-enrollments.*') ? 'text-[#af101a]' : '' }}"
                @if(request()->routeIs('pre-enrollments.*')) style="font-variation-settings: 'FILL' 1;" @endif
            >pending_actions</span>
            <span class="font-inter text-sm font-medium">Pré-inscription</span>
        </a>

        {{-- Paiement --}}
        <a
            href="{{ route('payments.index') }}"
            class="flex items-center space-x-3 rounded-lg p-3 transition-all {{ request()->routeIs('payments.*') ? 'bg-zinc-800/50 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/30' }}"
        >
            <span
                class="material-symbols-outlined {{ request()->routeIs('payments.*') ? 'text-[#af101a]' : '' }}"
                @if(request()->routeIs('payments.*')) style="font-variation-settings: 'FILL' 1;" @endif
            >payments</span>
            <span class="font-inter text-sm font-medium">Paiement</span>
        </a>

        {{-- Inscription Officielle --}}
        <a
            href="{{ route('enrollments.index') }}"
            class="flex items-center space-x-3 rounded-lg p-3 transition-all {{ request()->routeIs('enrollments.*') ? 'bg-zinc-800/50 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/30' }}"
        >
            <span
                class="material-symbols-outlined {{ request()->routeIs('enrollments.*') ? 'text-[#af101a]' : '' }}"
                @if(request()->routeIs('enrollments.*')) style="font-variation-settings: 'FILL' 1;" @endif
            >how_to_reg</span>
            <span class="font-inter text-sm font-medium">Inscription</span>
        </a>
    </nav>

    <div class="mt-auto pt-8 border-t border-zinc-800/50 space-y-1">
        <a class="flex items-center space-x-3 text-zinc-500 hover:text-white p-3 transition-all" href="#">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-inter text-sm font-medium">Paramètres</span>
        </a>

        {{-- Formulaire Déconnexion Sidebar --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 text-zinc-500 hover:text-white p-3 transition-all cursor-pointer">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-inter text-sm font-medium">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
