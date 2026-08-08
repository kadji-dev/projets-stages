<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-zinc-100 p-6 premium-shadow']) }}>
    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">{{ $label }}</p>
    <p class="font-montserrat text-3xl font-extrabold {{ $valueClass }}">{{ $value }}</p>
</div>
