<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Campus360 | Portail Étudiant')</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Material Symbols Outlined';
            font-style: normal;
            font-weight: 100 700;
            src: url('/fonts/material-symbols-outlined.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400 600;
            src: url('/fonts/inter.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400 800;
            src: url('/fonts/montserrat.woff2') format('woff2');
        }
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
        }
        body {
            background-color: #fff8f7;
            color: #271816;
            font-family: 'Inter', sans-serif;
        }
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-[#fff8f7] selection:bg-[#af101a]/10">

{{-- Appel du Composant Sidebar --}}
<x-student.sidebar />

<main class="md:ml-64 min-h-screen relative flex flex-col bg-[#FAFAFA]">
    <header class="h-20 flex items-center justify-between px-4 md:px-12 sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-zinc-100">
        <div class="flex items-center gap-4">
            <button class="md:hidden text-zinc-900 cursor-pointer">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h2 class="font-montserrat text-xl font-bold text-zinc-900">@yield('page-title', 'Tableau de bord')</h2>
        </div>

        <div class="flex items-center gap-4 relative">
            <div id="profile-dropdown-trigger" class="flex items-center gap-4 cursor-pointer">
                <div class="hidden md:flex flex-col items-end mr-1">
                    <span class="font-inter text-sm text-zinc-900 font-bold">{{ auth()->user()->name ?? 'Étudiant' }}</span>
                    <span class="font-inter text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">ID: {{ auth()->user()->enrollment?->matricule ?? 'Non Inscrit' }}</span>
                </div>
                <div class="w-11 h-11 rounded-full ring-2 ring-[#af101a]/10 overflow-hidden shadow-inner">
                    <img alt="Profile" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA89KWPbqphM_eOGB_6n0cloB--LEvzBvOTiTfx9gLbtONYhacHtboe6uZgZZ9oU3ICkdM5q1GqzNFEtuFi3XqzU2NwToMkliwY7sgmbrhyNI0mBGgTm8LIZRFdlu7E_87sdKmaJW-0H9--Xq3hTR2z19bC79mrhIQvMxOD7_vVBlgUFzXAW1sPq24C82J25_NNWPaYTmbpDrSyz-vHXClXD7SPNNdrKLuMlavYTj0Fa62BdNC9AGqvfTOKO5RJQ38nxg74VuQExQ">
                </div>
            </div>

            {{-- Dropdown Profile --}}
            <div id="profile-menu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-2xl border border-zinc-100 py-2 z-50">
                <button class="w-full flex items-center gap-3 px-4 py-2 text-sm font-montserrat text-zinc-700 hover:bg-zinc-50 hover:text-[#af101a] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">info</span>
                    <span>À propos</span>
                </button>
                <div class="h-px bg-zinc-100 my-1"></div>

                {{-- Formulaire Déconnexion Dropdown --}}
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

    @yield('content')

    <footer class="mt-auto px-4 md:px-12 py-8 border-t border-zinc-100 bg-white">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 max-w-[1440px] mx-auto w-full">
            <p class="font-inter text-[10px] text-zinc-400 uppercase tracking-widest font-semibold">© {{ date('Y') }} Campus360 Management Portal • Excellence Académique</p>
            <div class="flex gap-8">
                <a class="font-inter text-[10px] text-zinc-400 hover:text-[#af101a] uppercase tracking-widest font-bold transition-colors" href="#">Vie Privée</a>
                <a class="font-inter text-[10px] text-zinc-400 hover:text-[#af101a] uppercase tracking-widest font-bold transition-colors" href="#">Conditions</a>
                <a class="font-inter text-[10px] text-zinc-400 hover:text-[#af101a] uppercase tracking-widest font-bold transition-colors" href="#">Support</a>
            </div>
        </div>
    </footer>
</main>

<script>
(function() {
    const trigger = document.getElementById('profile-dropdown-trigger');
    const menu = document.getElementById('profile-menu');

    if (trigger && menu) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    }
})();
</script>
</body>
</html>
