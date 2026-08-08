<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Campus360 Admin | Portail de Gestion')</title>

    <!-- Tailwind CSS v4 via Vite -->
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
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
        .premium-shadow {
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 10px 20px -10px rgba(0, 0, 0, 0.02);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#fff8f7] selection:bg-[#af101a]/10">
    <x-staff.confirm-dialog />
    <x-staff.sidebar />

    <!-- Main Content Canvas -->
    <main class="md:ml-72 min-h-screen relative flex flex-col bg-[#FAFAFA]">
        <x-staff.topbar />

        <div class="flex-1 px-4 md:px-12 py-6">
            <!-- Fil d'ariane -->
            <nav class="mb-6">
                <a href="{{ route('staff-dashboard.dashboard') }}" class="text-xs font-semibold text-zinc-400 hover:text-[#af101a] transition-colors uppercase tracking-widest">Accueil</a>
            </nav>

            @yield('content')
        </div>

        <x-staff.footer />
    </main>

</body>
</html>
