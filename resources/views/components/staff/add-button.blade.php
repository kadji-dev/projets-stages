@props(['icon' => 'add'])

<button
    type="button"
    {{ $attributes->merge(['class' => 'bg-[#af101a] text-white px-6 py-3 rounded-xl font-bold text-sm inline-flex items-center gap-2 shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all cursor-pointer']) }}
>
    <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
    {{ $slot }}
</button>
