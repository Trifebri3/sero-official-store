import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue', // Jika pakai Vue
        './app/Livewire/**/*.php', // Support Volt & Livewire
    ],

    theme: {
        extend: {
            colors: {
                // Sesuai palet Luxury Interior di gambar
                'luxury': {
                    'cream': '#F9F7F2',    // Background Page
                    'gold': '#C5A358',     // Border/Aksen halus
                    'orange': '#E3965F',   // Primary Button (Explore More)
                    'dark': '#2D2D2D',     // Typography Utama
                    'soft': '#E5E7EB',     // Border tipis untuk card
                }
            },
            fontFamily: {
                // Playfair Display wajib ada di layout.blade agar render sempurna
                'serif': ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
                'sans': ['Inter', ...defaultTheme.fontFamily.sans],
            },
            // Menambahkan elevasi shadow halus khas desain UI Modern
            boxShadow: {
                'luxury': '0 10px 40px -10px rgba(0, 0, 0, 0.05)',
            }
        },
    },

    plugins: [forms],
};
