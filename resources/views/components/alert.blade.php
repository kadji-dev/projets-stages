{{-- Message d'erreur (ex: accès refusé par le Middleware) --}}
@if (session('error'))
    <div class="mb-6 flex items-center justify-between p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm font-medium shadow-sm" role="alert">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-xl">gpp_maybe</span>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="p-1 rounded-lg hover:bg-red-500/10 transition-colors">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
@endif

{{-- Message de succès --}}
@if (session('success'))
    <div class="mb-6 flex items-center justify-between p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 text-sm font-medium shadow-sm" role="alert">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-xl">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="p-1 rounded-lg hover:bg-green-500/10 transition-colors">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>
@endif
