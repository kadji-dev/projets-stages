@extends('layouts.app') {{-- ou le nom de ton layout général --}}

@section('content')
    <x-navbar />

    <main>
        <!-- Hero Section -->
        <section class="relative min-h-[90vh] flex items-center pt-16 overflow-hidden">
            <div class="absolute inset-0 z-0">
                <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuArhOkkvZHNfBrAMFAkv1ERxdEjv73xDJo4PufK_PizN9wsx086fUOP6sgVBHOJ74ICKg5L3Eckhp1PZU7eoOt1vha_lm2PgFqXIB_9TNcJJ5osxFJn0w5dn6g1n4G72BtOtsigyzeY1uf9V0Th0IOB81nYFAM8PxTe9wyZIuq3H424xUB_I0s1g9FpBrgb1SWudIXIZdxWwRngKOsCZ-9CUMLtjzU21IIcpfm-EZQVY9EVEf3i3d76FjSRRe3F9K1wtepPgl_AVA')"></div>
                <div class="absolute inset-0 bg-white/20"></div>
                <div class="absolute inset-0 hero-gradient"></div>
            </div>

            <div class="relative z-10 w-full max-w-7xl mx-auto px-6 md:px-12">
                <div class="max-w-3xl">
                    <h1 class="font-display font-bold text-4xl md:text-5xl text-on-surface mb-6 leading-tight">
                        Construisez votre avenir <br class="hidden md:block"/> avec <span class="text-primary">Campus360</span>
                    </h1>
                    <p class="text-lg text-on-surface-variant mb-8 max-w-xl">
                        Rejoignez une institution d'excellence où l'innovation technologique rencontre la rigueur académique pour former les leaders de demain.
                    </p>

                    <!-- Boutons d'action dynamiques selon l'état de connexion -->
                    <div class="flex flex-col sm:flex-row gap-4">
                            @auth
                            {{-- Si l'utilisateur est connecté --}}
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('staff-dashboard.dashboard') }}" class="bg-primary text-white px-8 py-4 rounded-lg font-medium shadow-lg hover:shadow-primary/30 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    Espace Administration
                                    <span class="material-symbols-outlined">admin_panel_settings</span>
                                </a>
                            @else
                                <a href="{{ route('student-dashboard.dashboard') }}" class="bg-primary text-white px-8 py-4 rounded-lg font-medium shadow-lg hover:shadow-primary/30 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    Accéder à mon espace Étudiant
                                    <span class="material-symbols-outlined">dashboard</span>
                                </a>
                            @endif
                        @else
                            {{-- Si le visiteur n'est pas connecté --}}
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-primary text-white px-8 py-4 rounded-lg font-medium shadow-lg hover:shadow-primary/30 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    S'inscrire (Nouveau Candidat)
                                    <span class="material-symbols-outlined">arrow_forward</span>
                                </a>
                            @endif

                            <a href="{{ route('login') }}" class="border-2 border-on-surface text-on-surface px-8 py-4 rounded-lg font-medium hover:bg-on-surface hover:text-white active:scale-95 transition-all flex items-center justify-center">
                                Se connecter
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Floating Stats -->
            <div class="absolute bottom-12 right-12 hidden lg:flex gap-6">
                <div class="glass-card p-6 rounded-xl text-center min-w-[140px]">
                    <div class="text-primary font-display font-bold text-3xl">95%</div>
                    <div class="text-on-surface-variant text-xs font-semibold mt-1">Insertion Pro</div>
                </div>
                <div class="glass-card p-6 rounded-xl text-center min-w-[140px]">
                    <div class="text-primary font-display font-bold text-3xl">2500+</div>
                    <div class="text-on-surface-variant text-xs font-semibold mt-1">anciens étudiants</div>
                </div>
            </div>
        </section>

        <!-- Programs Section -->
        <section class="py-20 bg-surface" id="programs">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <span class="text-primary font-semibold text-xs tracking-widest uppercase">NOS FORMATIONS</span>
                        <h2 class="font-display font-bold text-3xl text-on-surface mt-2">Découvrez nos pôles d'excellence</h2>
                    </div>
                    <p class="text-on-surface-variant max-w-md text-sm">
                        Des cursus conçus avec des partenaires industriels pour répondre aux exigences du marché global.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <x-program-card icon="payments" title="Commerce" description="Marketing stratégique, commerce international et négociation de haut niveau." />
                    <x-program-card icon="terminal" title="Technologie" description="Ingénierie logicielle, Cyber-sécurité et Intelligence Artificielle appliquée." />
                    <x-program-card icon="monitoring" title="Gestion" description="Audit, Contrôle de gestion et Management des organisations complexes." />
                </div>
            </div>
        </section>

        <!-- Why Campus360 Section -->
        <section class="py-20 bg-surface-container-lowest" id="about">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="text-center mb-16">
                    <h2 class="font-display font-bold text-3xl text-on-surface">Pourquoi choisir l'Campus360 ?</h2>
                    <div class="w-16 h-1 bg-primary mx-auto mt-4 rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-8 relative overflow-hidden rounded-2xl group min-h-[380px]">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDNKqzE5q0KMMC_wKpl4PZVBXuT9DZJc5LECxzm5cHzR6r6QQSC9GFG0irRPWKDitizLyQNCrYWJr0P7_0_V5u6pAf6ThNUX3oVUvsVdh68ExtcsuPaYxCUzDMRMBBTChtDYMLvU36qEIY0b-aRJKKS4wWFCeEV0WbbM8mXDgpzzww7N76dLs3frpgU0iXyUdQHXmxB4TgHJiINV6j9sb8yfHB8YWI7tssQ1iJY_we5RaNhxSCP5FWCdM6q1dVzpzAAcvfzEXL35g')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8">
                            <span class="bg-primary text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Équipement Premium</span>
                            <h3 class="text-white font-display font-bold text-2xl mt-3">Un Laptop pour chaque étudiant</h3>
                            <p class="text-white/80 text-sm max-w-md mt-2">Dès votre inscription, nous vous fournissons les outils technologiques nécessaires pour exceller dans vos projets.</p>
                        </div>
                    </div>

                    <div class="md:col-span-4 bg-primary text-white p-8 rounded-2xl flex flex-col justify-center items-center text-center">
                        <span class="material-symbols-outlined text-6xl mb-4">public</span>
                        <h3 class="font-display font-bold text-xl mb-2">Standards Internationaux</h3>
                        <p class="text-white/80 text-sm">Nos diplômes sont reconnus mondialement et alignés sur les standards académiques les plus stricts.</p>
                    </div>

                    <div class="md:col-span-4 bg-secondary-container text-on-secondary-container p-8 rounded-2xl">
                        <span class="material-symbols-outlined text-5xl text-primary mb-4">apartment</span>
                        <h3 class="font-display font-bold text-xl mb-2">Campus Moderne</h3>
                        <p class="text-on-secondary-container/80 text-sm">Des infrastructures de pointe au cœur de la ville pour un cadre d'apprentissage optimal.</p>
                    </div>

                    <div class="md:col-span-8 bg-surface-container-high p-8 rounded-2xl border border-outline-variant flex flex-col md:flex-row items-center gap-8">
                        <div class="flex-1">
                            <h3 class="font-display font-bold text-xl text-on-surface mb-2">Accompagnement de carrière</h3>
                            <p class="text-on-surface-variant text-sm">Bénéficiez d'un réseau de plus de 100 entreprises partenaires pour vos stages et premier emploi.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-primary">groups</span></div>
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-primary">work</span></div>
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-primary">trending_up</span></div>
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-primary">rocket_launch</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="bg-inverse-surface rounded-3xl p-12 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[100px] rounded-full"></div>
                    <h2 class="text-white font-display font-bold text-3xl md:text-4xl mb-4">Prêt à transformer votre carrière ?</h2>
                    <p class="text-white/70 text-base max-w-2xl mx-auto mb-8">Les admissions pour la rentrée académique sont ouvertes. Ne manquez pas l'opportunité de rejoindre l'élite.</p>

                    <div class="flex flex-wrap justify-center gap-4">
                        @auth
                            @if(auth()->user()->isStudent())
                                <a href="{{ route('student-dashboard.dashboard') }}" class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:scale-105 transition-transform active:scale-95">Mon Espace Étudiant</a>
                            @else
                                <a href="{{ route('staff-dashboard.dashboard') }}" class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:scale-105 transition-transform active:scale-95">Tableau de bord Admin</a>
                            @endif
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:scale-105 transition-transform active:scale-95">S'inscrire maintenant</a>
                            @endif
                            <a href="{{ route('login') }}" class="border border-white/30 text-white px-8 py-4 rounded-lg font-bold hover:bg-white/10 transition-all">Se connecter</a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
    </main>

    <x-footer />

@endsection
