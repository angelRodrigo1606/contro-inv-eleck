@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-theme-text']) }}>
    {{ $value ?? $slot }}
</label>
