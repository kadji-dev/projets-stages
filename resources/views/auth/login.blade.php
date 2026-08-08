@extends('layouts.app')

@section('title', 'Campus360 | Connexion')

@section('content')
    <!-- h-screen + overflow-hidden empêchent tout scroll -->
    <main class="flex h-screen w-full overflow-hidden">

        <!-- Visuel Gauche -->
        <x-auth.hero-sidebar />

        <!-- Formulaire de Connexion -->
        <section class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 md:px-12 bg-surface-container-lowest h-full overflow-y-auto">
            <div class="w-full max-w-[420px] flex flex-col py-6">

                <!-- En-tête / Logo -->
                <div class="mb-6 text-center lg:text-left">
                    <a href="/" class="inline-block">
                        <span class="font-display text-3xl font-bold text-primary">Campus360</span>
                    </a>
                    <h1 class="font-display text-2xl font-bold text-on-surface mt-2">Bon retour parmi nous</h1>
                    <p class="text-on-surface-variant mt-1 text-sm">Veuillez entrer vos identifiants pour continuer.</p>
                </div>

                <!-- Message de statut (ex: mot de passe réinitialisé) -->
                @if (session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-xs font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Affichage des erreurs globales de validation (identifiants invalides, trop de tentatives...) -->
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 text-xs font-medium">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulaire -->
                <form method="POST" action="{{ Route::has('login') ? route('login') : '#' }}" class="space-y-4">
                    @csrf

                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-xs font-semibold text-on-surface-variant ml-1">Adresse e-mail</label>
                        <input
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="w-full h-11 px-4 rounded-xl bg-surface-container border {{ $errors->has('email') ? 'border-red-500/60 focus:border-red-500 focus:ring-red-500/20' : 'border-outline-variant/60 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all text-sm"
                            placeholder="nom@gmail.com"
                            type="email"
                        />
                        @error('email')
                            <span class="text-xs text-red-500 ml-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center">
                            <label for="password" class="text-xs font-semibold text-on-surface-variant ml-1">Mot de passe</label>
                            @if (Route::has('password.request'))
                                <a class="text-primary text-xs font-semibold hover:underline" href="{{ route('password.request') }}">Oublié ?</a>
                            @endif
                        </div>
                        <input
                            id="password"
                            name="password"
                            required
                            class="w-full h-11 px-4 rounded-xl bg-surface-container border {{ $errors->has('password') ? 'border-red-500/60 focus:border-red-500 focus:ring-red-500/20' : 'border-outline-variant/60 focus:border-primary focus:ring-primary/20' }} focus:ring-2 outline-none transition-all text-sm"
                            placeholder="••••••••"
                            type="password"
                        />
                        @error('password')
                            <span class="text-xs text-red-500 ml-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center py-0.5">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20 accent-primary">
                            <span class="text-xs text-on-surface-variant">Se souvenir de moi</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full h-11 bg-primary hover:bg-primary-container text-white font-semibold rounded-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <span>Connexion</span>
                        <span class="material-symbols-outlined text-[20px]">login</span>
                    </button>
                </form>

                <!-- Redirection vers Inscription -->
                <p class="mt-5 text-center text-sm text-on-surface-variant">
                    Nouveau sur Campus360 ?
                    <a href="{{ route('register') }}" class="text-primary font-bold hover:underline ml-1">
                        Créer un compte
                    </a>
                </p>

                <!-- SSO -->
                {{-- <x-auth.social-sso /> --}}

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-on-surface-variant/70">
                        © {{ date('Y') }} Campus360 Higher Education Management.<br/>
                        <a href="#" class="hover:text-primary transition-colors">Politique de confidentialité</a> •
                        <a href="#" class="hover:text-primary transition-colors">Conditions d'utilisation</a>
                    </p>
                </div>

            </div>
        </section>
    </main>
@endsection
