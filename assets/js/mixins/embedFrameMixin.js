export default {
  data() {
    return {
      isEmbedVisible: false,
      isCursorHere: false,
      embedWidth: 0,
      embedHeight: 0,
    };
  },
  methods: {
    mouseEntered() {
      this.isCursorHere = true;
      setTimeout(() => {
        if (this.isCursorHere) {
          this.setEmbedPosition();
          this.$root.$emit("close-other-layouts", this.embedData.elementId);
          this.isEmbedVisible = true;
          this.$refs.embed.startPlayer();
        }
      }, 300); // user cursor should be under block for this time period
    },

    mouseLeave() {
      this.isCursorHere = false;
    },

    async hideVideo(elementId) {
      if (!(elementId && this.embedData.elementId === elementId)) {
        await this.resetEmbedStyles();
        this.isEmbedVisible = false;

        if (this.$refs.embed.isPlaying()) {
          this.$refs.embed.stopPlayer();
        }
      }
    },

    setEmbedPosition() {
      const rect = this.$refs.itemWrapper.getBoundingClientRect();
      const rootHeight = this.$refs.itemWrapper.offsetHeight;
      const rootWidth = this.$refs.itemWrapper.offsetWidth;
      const scaleSize = rootWidth / this.embedWidth; // 300 - video wrapper width
      const isRectInViewport =
        rect.left >= 0 &&
        rect.left + this.embedWidth + 20 <=
          (window.innerWidth || document.documentElement.clientWidth); // 320 - video wrapper width with little extra space

      this.$refs.embedWrapper.style.transform = `translateY(-50%) scale(${scaleSize})`;
      this.$refs.embedWrapper.style.opacity = "0";
      this.$refs.embedWrapper.style.top = window.scrollY + rect.top + rootHeight / 2 + "px";

      if (isRectInViewport) {
        this.$refs.embedWrapper.style.left = rect.left + "px";
      } else {
        this.$refs.embedWrapper.style.transformOrigin = "center center";
        this.$refs.embedWrapper.style.left = "";
        this.$refs.embedWrapper.style.right = "10px";
      }

      setTimeout(() => {
        this.$refs.embedWrapper.style.transform = "translateY(-50%) scale(1)";
        this.$refs.embedWrapper.style.opacity = "1";
      }, 1);
    },

    async resetEmbedStyles() {
      const rootWidth = this.$refs.itemWrapper.offsetWidth;
      const scaleSize = rootWidth / this.embedWidth;
      this.$refs.embedWrapper.style.transformOrigin = "left center";
      this.$refs.embedWrapper.style.transform = `translateY(-50%) scale(${scaleSize})`;
      this.$refs.embedWrapper.style.opacity = "0";

      await new Promise((resolve) => setTimeout( async () => {
        this.$refs.embedWrapper.style.left = "0";
        this.$refs.embedWrapper.style.top = "";
        this.$refs.embedWrapper.style.right = "";
        resolve()
      }, 500)); // value is equal to block transition duration
      // save top because of block jumpings
    },

    setEmbedSizes() {
      this.embedWidth = window.innerWidth > 1279 ? 400 : 300;
      this.embedHeight = window.innerWidth > 1279 ? 350 : 200;
    },
  },

  computed: {
    playBtnColor() {
      return this.embedName === "TwitchEmbed" ? "twitch" : "youtube";
    },
    showEmbed() {
      return (
        (this.showOnline && this.onlineDisplay.showEmbed) ||
        (!this.showOnline && this.offlineDisplay.showEmbed)
      );
    },

    showArt() {
      return (
        (this.showOnline && this.onlineDisplay.showArt) ||
        (!this.showOnline && this.offlineDisplay.showArt)
      );
    },

    showOverlay() {
      return (
        this.overlay &&
        ((this.showOnline && this.onlineDisplay.showOverlay) ||
          (!this.showOnline && this.offlineDisplay.showOverlay))
      );
    },

    embedSize() {
      return {
        width: this.embedWidth + 'px',
        height: this.embedHeight + 'px'
      }
    }
  },

  created() {
    window.addEventListener("resize", this.setEmbedSizes);
    this.setEmbedSizes();
  },
  
  mounted() {
    this.$root.$on("close-other-layouts", this.hideVideo);
    this.resetEmbedStyles();
  },

  destroyed() {
    this.$root.$off("close-other-layouts", this.hideVideo);
    window.removeEventListener("resize", this.setEmbedSizes);
  },
};