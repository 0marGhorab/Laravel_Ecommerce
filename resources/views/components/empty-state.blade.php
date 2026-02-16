@props([
    'title',
    'description' => null,
    'ctaText' => null,
    'ctaUrl' => null,
])

<div {{ $attributes->merge(['class' => 'card-cozy p-8 sm:p-12 text-center') }}>
    @if(isset($icon))
        <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-full bg-cream-100 text-warm/70 mb-4">
            {{ $icon }}
        </div>
    @endif
    <h3 class="text-lg font-semibold text-warm-darker">{{ $title }}</h3>
    @if($description)
        <p class="mt-2 text-sm text-warm/80 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    @if($ctaText && $ctaUrl)
        <div class="mt-6">
            <a href="{{ $ctaUrl }}" wire:navigate
               class="btn-cozy">
                {{ $ctaText }}
            </a>
        </div>
    @endif
    {{ $slot ?? '' }}
</div>
