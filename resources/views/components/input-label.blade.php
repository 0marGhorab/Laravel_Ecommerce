@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-warm-darker']) }}>
    {{ $value ?? $slot }}
</label>
