@props([
    'src' => null,
    'name' => '',
    'size' => 'md',
    'alt' => null,
])

@php
$sizes = match ($size) {
    'sm' => 'h-6 w-6 text-[0.6rem]',
    'lg' => 'h-20 w-20 text-2xl',
    default => 'h-8 w-8 text-xs',
};

$altText = $alt ?? __('Avatar of :name', ['name' => $name]);

if ($src) {
    $src = filter_var($src, FILTER_VALIDATE_URL) ? $src : Storage::url($src);
}

$initials = collect(explode(' ', trim($name)))
    ->filter()
    ->take(2)
    ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
    ->implode('');
@endphp

@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $altText }}"
        {{ $attributes->merge(['class' => "inline-block rounded-full object-cover shrink-0 {$sizes}"]) }}
    />
@else
    <span
        {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full bg-brand-primary text-white font-semibold uppercase tracking-wider shrink-0 {$sizes}"]) }}
        aria-label="{{ $altText }}"
        role="img"
    >
        {{ $initials ?: '?' }}
    </span>
@endif
