<div
    x-data
    x-show="$store.confirmDialog.show"
    x-cloak
    class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/40"
>
    <div
        @click.outside="$store.confirmDialog.cancel()"
        class="bg-white rounded-2xl w-full max-w-sm p-6 premium-shadow text-center"
    >
        <div
            class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4"
            :class="$store.confirmDialog.variant === 'danger' ? 'bg-red-50' : 'bg-[#af101a]/10'"
        >
            <span
                class="material-symbols-outlined text-2xl"
                :class="$store.confirmDialog.variant === 'danger' ? 'text-red-500' : 'text-[#af101a]'"
            >
                <span x-text="$store.confirmDialog.variant === 'danger' ? 'warning' : 'help'"></span>
            </span>
        </div>

        <h4 class="font-montserrat font-bold text-zinc-900 text-lg mb-2" x-text="$store.confirmDialog.title"></h4>
        <p class="text-sm text-zinc-500 mb-6" x-text="$store.confirmDialog.message"></p>

        <div class="flex gap-3">
            <button
                type="button"
                @click="$store.confirmDialog.cancel()"
                class="flex-1 px-5 py-2.5 rounded-xl border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50 cursor-pointer"
            >Annuler</button>
            <button
                type="button"
                @click="$store.confirmDialog.confirm()"
                class="flex-1 px-5 py-2.5 rounded-xl text-white text-sm font-bold cursor-pointer"
                :class="$store.confirmDialog.variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-[#af101a] hover:opacity-90'"
                x-text="$store.confirmDialog.confirmLabel"
            ></button>
        </div>
    </div>
</div>
