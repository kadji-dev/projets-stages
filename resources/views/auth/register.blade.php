<x-layouts.app title="ESCa | Créer un compte">
    <!-- h-screen + overflow-hidden empêchent tout scroll -->
    <main class="flex h-screen w-full overflow-hidden">

        <!-- Visuel Gauche -->
        <x-auth.hero-sidebar />

        <!-- Formulaire d'Inscription -->
        <section class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 md:px-12 bg-surface-container-lowest h-full overflow-y-auto">
            <div class="w-full max-w-[420px] flex flex-col py-6">

                <!-- En-tête / Logo -->
                <div class="mb-5 text-center lg:text-left">
                    <a href="/" class="inline-block">
                        <span class="font-display text-3xl font-bold text-primary">ESCa</span>
                    </a>
                    <h1 class="font-display text-2xl font-bold text-on-surface mt-2">Créer un compte</h1>
                    <p class="text-on-surface-variant mt-1 text-sm">Inscrivez-vous pour accéder à votre espace personnel.</p>
                </div>

                <!-- Formulaire -->
                <form method="POST" action="{{ Route::has('register') ? route('register') : '#' }}" class="space-y-3.5">
                    @csrf

                    <div class="flex flex-col gap-1">
                        <label for="name" class="text-xs font-semibold text-on-surface-variant ml-1">Nom complet</label>
                        <input
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="w-full h-11 px-4 rounded-xl bg-surface-container border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm"
                            placeholder="Jean Dupont"
                            type="text"
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="email" class="text-xs font-semibold text-on-surface-variant ml-1">Adresse e-mail</label>
                        <input
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full h-11 px-4 rounded-xl bg-surface-container border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm"
                            placeholder="nom@esca.cm"
                            type="email"
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="password" class="text-xs font-semibold text-on-surface-variant ml-1">Mot de passe</label>
                        <input
                            id="password"
                            name="password"
                            required
                            class="w-full h-11 px-4 rounded-xl bg-surface-container border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm"
                            placeholder="••••••••"
                            type="password"
                        />
                    </div>

                    <button type="submit" class="w-full h-11 bg-primary hover:bg-primary-container text-white font-semibold rounded-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer mt-2">
                        <span>Créer mon compte</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                </form>

                <!-- Redirection vers Connexion -->
                <p class="mt-4 text-center text-sm text-on-surface-variant">
                    Déjà inscrit ?
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline ml-1">
                        Connexion
                    </a>
                </p>

                <!-- SSO -->
                <x-auth.social-sso />

                <!-- Footer -->
                <div class="mt-5 text-center">
                    <p class="text-xs text-on-surface-variant/70">
                        © {{ date('Y') }} ESCa Higher Education Management.<br/>
                        <a href="#" class="hover:text-primary transition-colors">Politique de confidentialité</a> •
                        <a href="#" class="hover:text-primary transition-colors">Conditions d'utilisation</a>
                    </p>
                </div>

            </div>
        </section>
    </main>
</x-layouts.app>
