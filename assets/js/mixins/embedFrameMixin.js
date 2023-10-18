export default {
  data() {
    return {
      isEmbedVisible: false,
      isCursorHere: false,
      embedWidth: 0,
      embedHeight: 0,
      mouseDown: false,
      startX: 0,
      scrollLeft: 0
    };
  },
  methods: {
    mouseEntered() {
      // console.log('In mouseEntered');
      this.isCursorHere = true;
      // console.log('this.isCursorHere 1', this.isCursorHere);
      setTimeout(() => {
        // console.log('this.isCursorHere 2', this.isCursorHere);
        if (this.isCursorHere) {
          // console.log('Innn');
          this.setEmbedPosition();
          this.$root.$emit("close-other-layouts", this.embedData.elementId);
          this.isEmbedVisible = true;
          if (this.$refs.embed)
            this.$refs.embed.startPlayer();
        }
      }, 30); // user cursor should be under block for this time period
    },

    mouseLeave() {
      // console.log('In mouseLeave');
      this.isCursorHere = false;
    },

    async hideVideo(elementId) {
      // use the condition below for the Offline embed containers to auto-play and dont get closed
      // if (!(elementId && this.embedData.elementId === elementId) && this.showOnline) {
      // }
      if (!(elementId && this.embedData && this.embedData.elementId === elementId)) {
        await this.resetEmbedStyles();
        this.isEmbedVisible = false;

        if (this.$refs.embed) {
          if (this.$refs.embed.isPlaying()) {
            this.$refs.embed.stopPlayer();
          }
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
      // use the condition below for the Offline embed containers to auto-play and dont get closed
      // if (this.showOnline) {
      // }
      if (this.$refs.itemWrapper) {
        const rootWidth = this.$refs.itemWrapper.offsetWidth;
        const scaleSize = rootWidth / this.embedWidth;
        if (this.$refs.embedWrapper != undefined) {
          this.$refs.embedWrapper.style.transformOrigin = "left center";
          this.$refs.embedWrapper.style.transform = `translateY(-50%) scale(${scaleSize})`;
          this.$refs.embedWrapper.style.opacity = "0";

          await new Promise((resolve) => setTimeout(async () => {
            this.$refs.embedWrapper.style.left = "0";
            this.$refs.embedWrapper.style.top = "";
            this.$refs.embedWrapper.style.right = "";
            resolve()
          }, 500)); // value is equal to block transition duration
          // save top because of block jumpings
        }
      }

    },

    setEmbedSizes() {
      this.embedWidth = window.innerWidth > 1279 ? 400 : 355;
      this.embedHeight = window.innerWidth > 1279 ? 350 : 311;
    },
    startDragging(e) {
      this.$root.$emit('close-other-layouts');
      this.mouseDown = true;
      this.startX = e.pageX - this.$refs.channelBox.offsetLeft;
      this.scrollLeft = this.$refs.channelBox.scrollLeft;
      this.triggerDragging(e)
    },
    stopDragging(e) {
      this.mouseDown = false;
    },
    triggerDragging(e) {
      e.preventDefault();
      if (!this.mouseDown) {
        return;
      }
      const x = e.pageX - this.$refs.channelBox.offsetLeft;
      const scroll = x - this.startX;
      this.$refs.channelBox.scrollLeft = this.scrollLeft - scroll;
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