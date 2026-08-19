/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        crimson: {
          DEFAULT: '#C8101E',
          dark: '#A50D19',
          light: '#E84856',
        },
        burgundy: '#7A0C16',
        ivory: '#FFF9F7',
        gold: {
          DEFAULT: '#C9A227',
          light: '#E0C56E',
          dark: '#A8861E',
        },
        'soft-red': '#F9E6E8',
        charcoal: '#1F1F1F',
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      maxWidth: {
        '7xl': '80rem',
      },
    },
  },
  plugins: [],
};
