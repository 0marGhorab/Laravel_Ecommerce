<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-cozy']) }}>
    {{ $slot }}
</button>
