const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand': {
                    50: '#f5f7ff',
                    100: '#ebf0fe',
                    200: '#d6e0fd',
                    300: '#b3c7fc',
                    400: '#899ff8',
                    500: '#6676f3',
                    600: '#4f54e8',
                    700: '#4540d1',
                    800: '#3a36a9',
                    900: '#343485',
                },
            },
            aspectRatio: {
                '1/1': '1 / 1',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
