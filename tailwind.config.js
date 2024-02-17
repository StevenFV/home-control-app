const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // todosfv - configure primary and secondary colors with app.css
            colors: {
                primary: 'rgb(var(--color-primary) / <alpha-value)',
                secondary: 'rgb(var(--color-secondary) / <alpha-value)'
            }
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
