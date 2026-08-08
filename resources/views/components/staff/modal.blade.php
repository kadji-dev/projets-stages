<div
    x-show="modalOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
    style="display: none;"
>
    <div
        @click.outside="modalOpen = false"
        class="bg-white rounded-2xl w-full max-w-lg premium-shadow p-6 max-h-[90vh] overflow-y-auto"
    >
        <div class="flex items-center justify-between mb-6">
            <h4 class="font-montserrat font-bold text-lg text-zinc-900">
                <span x-text="mode === 'edit' ? 'Modifier — {{ $title }}' : 'Nouvelle — {{ $title }}'"></span>
            </h4>
            <button type="button" @click="modalOpen = false" class="text-zinc-400 hover:text-zinc-700 cursor-pointer">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{ $slot }}
    </div>
</div>
