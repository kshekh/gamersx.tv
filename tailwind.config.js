const colors = require('tailwindcss/colors')

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
        twitch: {
          DEFAULT: '#6441a5'
        }
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
