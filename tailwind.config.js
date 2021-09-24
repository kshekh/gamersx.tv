const colors = require("tailwindcss/colors");

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    colors: {
      purple: {
        DEFAULT: "#6441A5"
      },
      white: {
        DEFAULT: "#FFFFFF"
      },
      black: {
        DEFAULT: "#000612"
      },
      grey: {
        DEFAULT: "#737373"
      },
      red: {
        DEFAULT: "#FF0000"
      }
    },
    boxShadow: {
      twitch: "0px 0px 40px #6441A5",
      youtube: "0px 0px 15px rgba(255, 0, 0, 0.25)",
      none: "none"
    },
    dropShadow: {
      twitch: "0px 0px 10px rgba(100, 65, 165, 0.8)",
      youtube: "0px 0px 10px rgba(255, 0, 0, 0.6)"
    },
    extend: {
      fontSize: {
        xxs: [
          "0.63rem", // 10px
          {
            lineHeight: "1.2em", // 12px
          },
        ],
        sm: [
          "0.875rem", // 14px
          {
            lineHeight: "1.214em", // 17px
          },
        ],
        lg: [
          "1.125rem", // 18px
          {
            lineHeight: "1.1666em", // 21px
          },
        ],
        "2xl": [
          "1.5rem", // 24px
          {
            lineHeight: "1.22em", // 29.3px
          },
        ],
        "4xl": [
          "2.25rem", // 36px
          {
            lineHeight: "1.111em", // 41px
          },
        ],
      }
    }
  },
  variants: {
    extend: {}
  },
  plugins: []
};
