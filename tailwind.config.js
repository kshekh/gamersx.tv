/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/js/components/**/*.{js,vue,ts}",    
    "./templates/**/*.twig",    
  ],
  theme: {
    screens: {
      'xs': '480px', // Your custom screen size
      'sm': '640px',
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      '2xl': '1536px',
      'wide': {
          'raw': `only screen and (max-height: 480px) and (max-width: 960px)`
      },
    },
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
        DEFAULT: "#737373",
        400: "#dfdfdf",
        600: "#C3C3C3",
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
        "20": "20px",
        "30": "30px",
        "40": "40px",
        "50": "50px",
        "75": "75px",
        "70": "70px",
        "90": "90px",
        "130": "130px",
        "180": "180px",
        "240": "240px"
      },
      maxWidth: {
        "1/3": "33%",
        "1/4": "25%",
        "116": "116px",
        "224": "224px",
        "485": "485px",
        "full-hd": "1920px"
      },
      spacing: {
        "0.75": "3px",
        "18": "72px",
        "22": "88px",
        "25p": "25%",
        "30p": "30%",
        "35p": "35%",
        "40p": "40%",
        "45p": "45%",
        "26": "104px",
        "27": "108px",
        "30": "120px",
        "34": "136px",
        "41": "164px",
        "45": "180px",
        "46": "184px",
        "50": "200px",
        "54": "216px",
        "60": "240px",
        "75": "300px",
        "86": "344px",
        "90": "360px",
        "92": "384px",
        "118": "472px",
        "120": "480px",
        "127": "510px",
        "150": "600px",
        "160": "640px",
        "50p": "50%",
        "60p": "60%",
        "110p": "110%",
        "115p": "115%",
        "140p": "140%",
        "150p": "150%",
        "230p": "230%",
        "330p": "330%",
        "360p": "360%",
        "360p": "360%",
        "337": "337px"
      },
      zIndex: {
        negative: -1
      },
      inset: {
        "1/5": "20%"
      },
      transitionProperty: {
        inset: "inset",
        left: "left",
        "opacity-transform": "opacity, transform"
      },
      transitionDelay: {
        750: "750ms"
      },
      backgroundSize: {
        "full": "100% 100%",
      },
      boxShadow: {
        "smooth": "0px 0px 27px 25px #e1e1e12e, inset 0px 0px 43px 130px #e1e1e12e",
      },
      backdropBlur: {
        xs: "2px",
      },
      aspectRatio: {
        '4/3': '4 / 3',
        '3/4': '3 / 4',
      }
    }
  },  
  plugins: []
};
