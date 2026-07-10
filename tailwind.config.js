const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#e6f4ff',
                    100: '#cceaff',
                    200: '#99d5ff',
                    300: '#66c0ff',
                    400: '#33abff',
                    500: '#0194F3', // PRIMARY
                    600: '#0177c6',
                    700: '#015a95',
                    800: '#013e66',
                    900: '#00233a',
                },
            },
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 10px 30px rgba(2, 132, 199, 0.15)',
            },
            borderRadius: {
                xl2: '1.25rem',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/line-clamp'),
    ],
};
