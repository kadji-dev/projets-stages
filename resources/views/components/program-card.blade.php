@props(['icon', 'title', 'description', 'link' => '#'])

<div class="bg-white border border-outline-variant p-8 rounded-xl hover:shadow-xl hover:border-primary/20 transition-all group flex flex-col justify-between">
    <div>
        <div class="w-16 h-16 bg-surface-container-low rounded-lg flex items-center justify-center mb-6 text-primary group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-3xl">{{ $icon }}</span>
        </div>
        <h3 class="font-display font-semibold text-xl text-on-surface mb-3">{{ $title }}</h3>
        <p class="text-on-surface-variant text-sm leading-relaxed mb-6">{{ $description }}</p>
    </div>
    <a href="{{ $link }}" class="text-primary font-medium text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
        En savoir plus
        <span class="material-symbols-outlined text-sm">east</span>
    </a>
</div>
