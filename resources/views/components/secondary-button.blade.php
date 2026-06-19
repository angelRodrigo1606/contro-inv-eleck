<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-cream border border-brand-midnight/20 rounded-md font-semibold text-xs text-brand-midnight uppercase tracking-widest shadow-sm hover:bg-brand-secondary/30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
