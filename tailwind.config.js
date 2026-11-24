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
                juniper: {
                    50: '#eef3f1',
                    100: '#d5e2dc',
                    200: '#abc6ba',
                    300: '#7da797',
                    400: '#4f8571',
                    500: '#2f5d4c',
                    600: '#1f3d33',
                    700: '#173028',
                    800: '#11241e',
                    900: '#0b1814',
                },
                toffee: {
                    50: '#f8efe8',
                    100: '#efdccb',
                    200: '#e0b99b',
                    300: '#cf936b',
                    400: '#b9743f',
                    500: '#a25f31',
                    600: '#8a4b2a',
                    700: '#6e3b21',
                    800: '#552e1b',
                    900: '#3d2113',
                },
                alabaster: {
                    50: '#fbf8f2',
                    100: '#f5edde',
                    200: '#ece0c9',
                    300: '#dfcfb0',
                },
                gold: {
                    300: '#e3cc90',
                    400: '#d8bb74',
                    500: '#c9a65a',
                    600: '#b08d43',
                    700: '#8f7136',
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
