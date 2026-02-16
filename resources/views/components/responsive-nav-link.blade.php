@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-warm text-start text-base font-medium text-warm-darker bg-cream-100 focus:outline-none transition duration-200'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-warm/80 hover:text-warm-darker hover:bg-cream-50 hover:border-cream-300 focus:outline-none transition duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
