@php
    $styles = [
        'success' => 'bg-[#006444]/10 text-[#006444]',
        'warning' => 'bg-amber-100 text-amber-600',
        'danger'  => 'bg-red-100 text-red-500',
        'accent'  => 'bg-[#af101a]/10 text-[#af101a]',
        'default' => 'bg-zinc-100 text-zinc-500',
    ];
    $classes = $styles[$status] ?? $styles['default'];
@endphp

<span {{ $attributes->merge(['class' => "text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full inline-block $classes"]) }}>
    {{ $slot }}
</span>
