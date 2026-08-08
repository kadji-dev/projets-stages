@php
    $adminLinks = [
        ['route' => 'staff-dashboard.dashboard', 'icon' => 'grid_view', 'label' => "Vue d'ensemble"],
        ['route' => 'staff-dashboard.admissions', 'icon' => 'person_add', 'label' => 'Admissions'],
        ['route' => 'staff-dashboard.payments', 'icon' => 'account_balance_wallet', 'label' => 'Paiements'],
        ['route' => 'staff-dashboard.pc-stock', 'icon' => 'computer', 'label' => 'Stock PC'],
    ];

    $academicLinks = [
        ['route' => 'academic.cursuses', 'icon' => 'auto_stories', 'label' => 'Cursus'],
        ['route' => 'academic.fields', 'icon' => 'account_tree', 'label' => 'Filières'],
        ['route' => 'academic.specialities', 'icon' => 'workspace_premium', 'label' => 'Spécialités'],
        ['route' => 'academic.levels', 'icon' => 'stairs', 'label' => 'Niveaux'],
        ['route' => 'academic.years', 'icon' => 'calendar_month', 'label' => 'Années Académiques'],
    ];
@endphp

<aside class="h-screen w-72 fixed left-0 top-0 bg-[#121212] flex flex-col py-8 px-4 z-40 hidden md:flex shadow-2xl">
    <div class="mb-10 px-2 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#af101a] flex items-center justify-center shadow-lg shadow-[#af101a]/30">
            <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <h1 class="font-montserrat text-xl text-white font-bold tracking-tight">Campus360 Admin</h1>
    </div>

    <nav class="flex-1 space-y-8 overflow-y-auto">
        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-[0.15em]">Gestion Administrative</p>
            @foreach ($adminLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="flex items-center space-x-3 rounded-xl p-3 transition-all {{ request()->routeIs($link['route']) ? 'bg-[#af101a] text-white shadow-lg shadow-[#af101a]/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}"
                >
                    <span class="material-symbols-outlined text-[20px]">{{ $link['icon'] }}</span>
                    <span class="font-inter text-sm font-medium">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-bold text-zinc-500 uppercase tracking-[0.15em]">Gestion Académique</p>
            @foreach ($academicLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="flex items-center space-x-3 rounded-xl p-3 transition-all {{ request()->routeIs($link['route']) ? 'bg-[#af101a] text-white shadow-lg shadow-[#af101a]/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/40' }}"
                >
                    <span class="material-symbols-outlined text-[20px]">{{ $link['icon'] }}</span>
                    <span class="font-inter text-sm font-medium">{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    <div class="mt-auto pt-6 border-t border-zinc-800/50 space-y-1">
        <a class="flex items-center space-x-3 text-zinc-500 hover:text-white p-3 rounded-xl hover:bg-zinc-800/40 transition-all" href="#">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span class="font-inter text-sm font-medium">Paramètres</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 text-zinc-500 hover:text-white p-3 rounded-xl hover:bg-zinc-800/40 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span class="font-inter text-sm font-medium">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
