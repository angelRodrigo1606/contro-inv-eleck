@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-brand-midnight']) }}>
    {{ $value ?? $slot }}
</label>
