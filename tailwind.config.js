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
            colors: {
                // MAIN (60%) - CREAM #F5DABF - backgrounds, cards, large surfaces
                cream: {
                    50: '#fef9f0',
                    100: '#fdf0e0',
                    200: '#fae1c1',
                    300: '#f7d2a2',
                    400: '#f5c383',
                    500: '#F5DABF',  // Base CREAM
                    600: '#e6c4a6',
                    700: '#d4a885',
                    800: '#b88a65',
                    900: '#96704f',
                },
                // SECONDARY (30%) - GREEN #0F3D3A - primary actions, navigation, headings, buttons
                green: {
                    50: '#e8f0ef',
                    100: '#d1e1e0',
                    200: '#a3c3c1',
                    300: '#75a5a2',
                    400: '#478783',
                    500: '#196966',
                    600: '#125250',
                    700: '#0F3D3A',  // Base GREEN
                    800: '#0c312f',
                    900: '#092523',
                },
                // ACCENT (10%) - BORDO #6C151E - CTAs, highlights, focus rings, special elements
                bordo: {
                    50: '#fdf0f1',
                    100: '#fbe1e3',
                    200: '#f5c3c7',
                    300: '#eda5ab',
                    400: '#e27780',
                    500: '#d64a59',
                    600: '#a83a47',
                    700: '#6C151E',  // Base BORDO
                    800: '#561118',
                    900: '#430d13',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};