@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-cream-300 focus:border-warm focus:ring-warm/30 rounded-lg shadow-sm bg-white text-warm-darker placeholder:text-warm/50 transition duration-200']) }}>
