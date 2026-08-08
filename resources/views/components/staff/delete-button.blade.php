<form method="POST" action="{{ $action }}" id="{{ $formId }}" class="hidden">
    @csrf
    @method('DELETE')
</form>

<button
    type="button"
    x-data
    @click="
        $store.confirmDialog.title = {{ \Illuminate\Support\Js::from($title) }};
        $store.confirmDialog.message = {{ \Illuminate\Support\Js::from($message) }};
        $store.confirmDialog.pendingForm = document.getElementById('{{ $formId }}');
        $store.confirmDialog.variant = 'danger';
        $store.confirmDialog.confirmLabel = 'Supprimer';
        $store.confirmDialog.show = true;
    "
    {{ $attributes->merge(['class' => 'px-4 py-2 rounded-lg border border-zinc-200 text-xs font-bold text-zinc-400 hover:border-red-300 hover:text-red-500 cursor-pointer inline-flex items-center gap-1']) }}
>
    <span class="material-symbols-outlined text-sm">delete</span>
    Supprimer
</button>
