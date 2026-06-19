@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-secondary text-sm font-medium leading-5 text-white focus:outline-none focus:border-brand-cream transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-brand-cream hover:text-white hover:border-brand-secondary/50 focus:outline-none focus:text-white focus:border-brand-secondary/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
