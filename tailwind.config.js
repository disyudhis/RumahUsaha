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
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', 'sans-serif'],
                aleo: ['Aleo', 'serif'],
                acme: ['Acme', 'sans-serif'],
            },
            colors: {
                fix : {
                    100: '#7B4019',
                    200: '#FFBF78',
                    300: '#FFEEA9',
                    400: '#FF7D29',
                },
                // Primary Brand Colors (Orange/Amber theme)
                primary: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                    950: '#431407',
                },
                // Secondary Colors (Warm Brown)
                secondary: {
                    50: '#fdf8f6',
                    100: '#f2e8e5',
                    200: '#eaddd7',
                    300: '#e0cec7',
                    400: '#d2bab0',
                    500: '#bfa094',
                    600: '#a18072',
                    700: '#977669',
                    800: '#846358',
                    900: '#43302b',
                },
                // Accent Colors (Warm Beige/Cream)
                accent: {
                    50: '#fefdfbf',
                    100: '#fef7f0',
                    200: '#f7e6d3',
                    300: '#f0d5b6',
                    400: '#e9c499',
                    500: '#e2b47c',
                    600: '#d4975a',
                    700: '#b8804a',
                    800: '#9c693a',
                    900: '#80522a',
                },
                // Success Colors (Keep default green but warmer)
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
                // Custom neutral colors with warm tint
                neutral: {
                    50: '#fafaf9',
                    100: '#f5f5f4',
                    200: '#e7e5e4',
                    300: '#d6d3d1',
                    400: '#a8a29e',
                    500: '#78716c',
                    600: '#57534e',
                    700: '#44403c',
                    800: '#292524',
                    900: '#1c1917',
                    950: '#0c0a09',
                }
            },
        backgroundImage: {
                'warm-gradient': 'linear-gradient(135deg, #fed7aa 0%, #fdba74 50%, #fb923c 100%)',
                'hero-gradient': 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 50%, #fed7aa 100%)',
                'card-gradient': 'linear-gradient(145deg, #ffffff 0%, #fafaf9 100%)',
            },
            boxShadow: {
                'warm': '0 4px 6px -1px rgba(251, 146, 60, 0.1), 0 2px 4px -1px rgba(251, 146, 60, 0.06)',
                'warm-lg': '0 10px 15px -3px rgba(251, 146, 60, 0.1), 0 4px 6px -2px rgba(251, 146, 60, 0.05)',
                'warm-xl': '0 20px 25px -5px rgba(251, 146, 60, 0.1), 0 10px 10px -5px rgba(251, 146, 60, 0.04)',
            }
        },
    },

    plugins: [forms],
};
