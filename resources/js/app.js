import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    theme: localStorage.getItem('theme')
        || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

    init() {
        this.apply();
    },

    toggle() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        this.apply();
    },

    apply() {
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', this.theme);
    },
}));

Alpine.start();
