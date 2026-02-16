import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cream: {
                    DEFAULT: '#faf5ef',
                    50: '#fdfcfa',
                    100: '#faf5ef',
                    200: '#f5ede3',
                    300: '#ebe0d2',
                },
                warm: {
                    DEFAULT: '#a4907c',
                    light: '#c4b8a8',
                    dark: '#8b7a6a',
                    darker: '#5c5346',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.4s ease-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'spin-4': 'spin4 0.5s ease-out',
            },
            keyframes: {
                spin4: {
                    '0%': { transform: 'rotate(0deg)' },
                    '100%': { transform: 'rotate(1440deg)' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { opacity: '0', transform: 'translateX(8px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
            },
            boxShadow: {
                'cozy': '0 2px 12px rgba(92, 83, 70, 0.08)',
                'cozy-lg': '0 8px 24px rgba(92, 83, 70, 0.12)',
            },
        },
    },

    plugins: [forms],
};
