@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-warm text-sm font-medium leading-5 text-warm-darker focus:outline-none transition duration-200'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-warm/70 hover:text-warm-darker hover:border-cream-300 focus:outline-none transition duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
