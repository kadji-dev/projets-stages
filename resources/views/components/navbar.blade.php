<nav class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-xl border-b border-outline-variant/30 shadow-sm h-16">
    <div class="flex justify-between items-center px-6 md:px-12 h-full max-w-7xl mx-auto">
        <a href="{{ route('welcome') }}" class="font-display font-bold text-2xl text-primary">Campus360</a>

        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('welcome') }}" class="font-sans text-primary font-bold border-b-2 border-primary pb-0.5">Accueil</a>
            <a href="{{ route('welcome') }}#programs" class="font-sans text-on-surface-variant hover:text-primary transition-colors">Programmes</a>
            <a href="{{ route('welcome') }}#about" class="font-sans text-on-surface-variant hover:text-primary transition-colors">À propos</a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                {{-- Bouton Tableau de bord si connecté --}}
                @if(auth()->user()->isStudent())
                    <a href="{{ route('student-dashboard.dashboard') }}" class="bg-primary text-white font-medium px-5 py-2.5 rounded-lg shadow-md hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">dashboard</span>
                        Mon Espace
                    </a>
                @else
                    <a href="{{ route('staff-dashboard.dashboard') }}" class="bg-primary text-white font-medium px-5 py-2.5 rounded-lg shadow-md hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                        Espace Staff
                    </a>
                @endif
            @else
                {{-- Boutons Se connecter / S'inscrire si visiteur --}}
                <a href="{{ route('login') }}" class="hidden sm:block text-primary font-medium px-4 py-2 hover:bg-surface-container-low rounded-lg transition-colors">
                    Se connecter
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-primary text-white font-medium px-5 py-2.5 rounded-lg shadow-md hover:scale-105 active:scale-95 transition-all">
                        S'inscrire
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>
