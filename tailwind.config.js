const colors = require('tailwindcss/colors')

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
        twitch: {
          DEFAULT: '#9146ff'
        }
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
