/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                // Your new primary - Ember Neon Orange
                ember: {
                    50: '#FFF4ED',
                100: '#FFE6D5',
                200: '#FDBA9C',
                300: '#FF8A3D',
                400: '#FF6B1A', // <- primary
                500: '#F4530A', // <- hover
                600: '#E03E00', // <- active / text on white
                700: '#B93300',
                800: '#7C2500',
                900: '#431407',
                },
                // Alias so you can write bg-brand-500 etc.
                brand: {
                    DEFAULT: '#FF6B1A',
                    light: '#FFE6D5',
                    dark: '#E03E00',
                },
            },
            // Optional: make orange the default ring / focus
            ringColor: {
                DEFAULT: '#FF6B1A',
            },
        },
    },
    plugins: [],
}