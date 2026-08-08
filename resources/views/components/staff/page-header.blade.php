<div class="flex items-start justify-between flex-wrap gap-4">
    <div>
        <h3 class="font-montserrat text-3xl font-extrabold text-zinc-900 tracking-tight">{{ $title }}</h3>
        @if ($subtitle)
            <p class="font-inter text-zinc-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        {{ $actions }}
    @elseif ($actionLabel)
        <button
            type="button"
            @if($actionUrl) onclick="window.location.href='{{ $actionUrl }}'" @endif
            class="bg-[#af101a] text-white px-6 py-3 rounded-xl font-bold text-sm inline-flex items-center gap-2 shadow-lg shadow-[#af101a]/20 hover:scale-105 transition-all cursor-pointer"
        >
            <span class="material-symbols-outlined text-lg">{{ $actionIcon }}</span>
            {{ $actionLabel }}
        </button>
    @endisset
</div>
