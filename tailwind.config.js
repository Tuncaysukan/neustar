import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, daisyui],

    daisyui: {
        themes: [
            {
                neustar: {
                    'color-scheme': 'light',
                    primary: '#1bb6ad',
                    'primary-content': '#eaffff',
                    secondary: '#0f7f7a',
                    'secondary-content': '#eaffff',
                    accent: '#39c6b4',
                    'accent-content': '#052d2f',
                    neutral: '#0b3a44',
                    'neutral-content': '#e8fbfb',
                    'base-100': '#ffffff',
                    'base-200': '#f6f8f9',
                    'base-300': '#e7edf0',
                    'base-content': '#0a1f24',
                    info: '#0ea5e9',
                    success: '#16a34a',
                    warning: '#f59e0b',
                    error: '#ef4444',
                },
            },
            {
                'neustar-dark': {
                    'color-scheme': 'dark',
                    primary: '#1bb6ad',
                    'primary-content': '#031b1d',
                    secondary: '#0f7f7a',
                    'secondary-content': '#eaffff',
                    accent: '#39c6b4',
                    'accent-content': '#031b1d',
                    neutral: '#062c33',
                    'neutral-content': '#e8fbfb',
                    'base-100': '#071a1f',
                    'base-200': '#06161a',
                    'base-300': '#0b252b',
                    'base-content': '#e8fbfb',
                    info: '#0ea5e9',
                    success: '#16a34a',
                    warning: '#f59e0b',
                    error: '#ef4444',
                },
            },
        ],
        darkTheme: 'neustar-dark',
        base: true,
        styled: true,
        utils: true,
        logs: false,
    },
};
