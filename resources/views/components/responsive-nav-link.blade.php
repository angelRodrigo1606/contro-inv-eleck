@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-secondary text-start text-base font-medium text-white bg-brand-primary/30 focus:outline-none focus:text-white focus:bg-brand-primary/40 focus:border-brand-secondary transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-brand-cream hover:text-white hover:bg-brand-primary/30 hover:border-brand-secondary/50 focus:outline-none focus:text-white focus:bg-brand-primary/30 focus:border-brand-secondary/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
