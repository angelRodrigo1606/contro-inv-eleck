@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text placeholder-theme-text-muted']) }}>
