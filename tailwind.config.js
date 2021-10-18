const colors = require("tailwindcss/colors");

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    colors: {
      purple: {
        600: "#32235B",
        DEFAULT: "#6441A5"
      },
      white: {
        DEFAULT: "#FFFFFF"
      },
      black: {
        DEFAULT: "#000612"
      },
      grey: {
        600: "#C3C3C3",
        DEFAULT: "#737373",
        900: "#1e1927"
      },
      red: {
        DEFAULT: "#FF0000"
      },
      transparent: {
        DEFAULT: "transparent"
      }
    },
    boxShadow: {
      base: "0px 0px 20px #6441A5",
      twitch: "0px 0px 40px #6441A5",
      youtube: "0px 0px 15px rgba(255, 0, 0, 0.25)",
      none: "none"
    },
    dropShadow: {
      twitch: "0px 0px 10px rgba(100, 65, 165, 0.8)",
      youtube: "0px 0px 10px rgba(255, 0, 0, 0.6)"
    },
    fontFamily: {
      play: "Play, sans-serif",
      calibri: "Calibri, sans-serif",
      bahnschrift: "Bahnschrift, sans-serif"
    },
    extend: {
      fontSize: {
        7: [
          "0.46rem", // 7px
          {
            lineHeight: "1.11em" // 8px
          }
        ],
        8: [
          "0.5rem", // 8px
          {
            lineHeight: "1.18em" // 9px
          }
        ],
        xxs: [
          "0.63rem", // 10px
          {
            lineHeight: "1.2em" // 12px
          }
        ],
        sm: [
          "0.875rem", // 14px
          {
            lineHeight: "1.214em" // 17px
          }
        ],
        lg: [
          "1.125rem", // 18px
          {
            lineHeight: "1.1666em" // 21px
          }
        ],
        "2xl": [
          "1.5rem", // 24px
          {
            lineHeight: "1.22em" // 29.3px
          }
        ],
        "4xl": [
          "2.25rem", // 36px
          {
            lineHeight: "1.111em" // 41px
          }
        ],
        xxl: [
          "9rem" // 144px
        ],
        "3xxl": [
          "11rem" // 176px
        ]
      },
      lineHeight: {
        "extra-tight": "0.6"
      },
      width: {
        "1px": "1px",
        "18": "4.5rem"
      },
      height: {
        "18": "72px"
      },
      minHeight: {
        mobile: "295px",
        "mobile-description": "250px"
      },
      minWidth: {
        "40": "40px",
        "50": "50px",
        "75": "75px",
        "130": "130px"
      },
      maxWidth: {
        "1/3": "33%",
        "116": "116px",
        "224": "224px",
        "485": "485px"
      },
      spacing: {
        "0.75": "3px",
        "18": "72px",
        "22": "88px",
        "26": "104px",
        "34": "136px",
        "41": "164px",
        "46": "184px",
        "86": "344px",
        "118": "472px",
        "50p": "50%",
        "110p": "110%",
        "115p": "115%",
        "140p": "140%",
        "150p": "150%",
        "230p": "230%",
        "330p": "330%",
        "360p": "360%",
        "360p": "360%"
      },
      zIndex: {
        negative: -1
      },
      inset: {
        "1/5": "20%"
      },
      transitionProperty: {
        inset: "inset",
        left: "left"
      }
    }
  },
  variants: {
    extend: {
      maxWidth: ["hover"],
      width: ["hover"],
      maxHeight: ["hover"],
      height: ["hover"],
      zIndex: ["hover"],
      inset: ["hover"],
      left: ["hover"]
    }
  },
  plugins: []
};
