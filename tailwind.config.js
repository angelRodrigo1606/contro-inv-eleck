import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"SF Pro Display"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    primary: '#2451C9',
                    secondary: '#F5B042',
                    cream: '#FFE2A8',
                    midnight: '#1C2E5C',
                    soft: '#F9F9FB',
                },
                theme: {
                    bg: 'var(--color-bg-base)',
                    surface: 'var(--color-surface)',
                    'surface-alt': 'var(--color-surface-alt)',
                    text: 'var(--color-text)',
                    'text-muted': 'var(--color-text-muted)',
                    border: 'var(--color-border)',
                    'input-border': 'var(--color-input-border)',
                },
            },
        },
    },

    plugins: [forms],
};
