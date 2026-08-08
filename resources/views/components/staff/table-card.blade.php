<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-zinc-100 premium-shadow overflow-hidden']) }}>
    @if ($title)
        <div class="flex items-center justify-between gap-4 p-6 border-b border-zinc-100">
            <div class="flex items-center gap-3">
                @if ($icon)
                    <span class="material-symbols-outlined text-[#af101a]">{{ $icon }}</span>
                @endif
                <h4 class="font-montserrat font-bold text-zinc-900 text-lg">{{ $title }}</h4>
            </div>
            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    @endif

    {{ $slot }}
</div>
