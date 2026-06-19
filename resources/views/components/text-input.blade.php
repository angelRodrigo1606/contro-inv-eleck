@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-white text-brand-midnight']) }}>
