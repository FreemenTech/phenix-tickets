<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-flame']) }}>
    {{ $slot }}
</button>
