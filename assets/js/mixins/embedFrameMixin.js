export default {
  data() {
    return {
      startX: 0,
      embedWidth: 0,
      scrollLeft: 0,
      embedHeight: 0,

      isPined: false,
      mouseDown: false,
      isCursorHere: false,
      isEmbedVisible: false,

      isPinBtnActive: false,
      isMoveBtnActive: false,
    };
  },

  methods: {
    mouseEntered() {
      if (this.$root.isVisibleVideoContainer) {
        return;
      }

      this.isCursorHere = true;
      this.$root.isVisibleVideoContainer = true;

      if (this.isCursorHere && this.$root.isVisibleVideoContainer) {
        setTimeout(() => {
          this.setEmbedPosition();

          this.$root.$emit("close-other-layouts", this.embedData.elementId);

          if (this.$refs.embed) this.$refs.embed.startPlayer();
        }, 30);
      }
    },

    clickContainer(elementId) {

      if (
        (!!this.$root.containerId && this.$root.containerId === elementId) ||
        this.$root.isPinnedContainer
      )
        return;

      if (
        (!!this.$root.containerId && this.$root.containerId !== elementId) ||
        this.$root.isPinnedContainer === false
      ) {
        setTimeout(() => {
          this.setEmbedPosition();

          this.$root.$emit("close-other-layouts", this.embedData.elementId);

          this.isEmbedVisible = true;

          if (this.$refs.embed) this.$refs.embed.startPlayer();
        }, 30);
      }

      this.$root.containerId = elementId;

      this.isCursorHere = true;

      this.$root.isVisibleVideoContainer = true;

      if (
        this.isCursorHere &&
        this.$root.isVisibleVideoContainer &&
        !this.$root.isPinnedContainer
      ) {
        setTimeout(() => {
          this.setEmbedPosition();

          this.isEmbedVisible = true;

          if (this.$refs.embed) this.$refs.embed.startPlayer();
        }, 30);
      }
    },

    mouseLeave() {
      this.isCursorHere = false;
    },

    closeContainer() {
      this.isPined = false;
      this.isPinBtnActive = false;
      this.$root.isVisibleVideoContainer = false;
      this.$root.isPinnedContainer = false;
      this.$root.containerId = "";

      this.resetEmbedStyles();

      this.isEmbedVisible = false;

      if (this.$refs.embed && this.$refs.embed.isPlaying()) {
        this.$refs.embed.stopPlayer();
      }
    },

    async hideVideo(elementId) {
      // use the condition below for the Offline embed containers to auto-play and dont get closed

      if (
        !(elementId && this.embedData && this.embedData.elementId === elementId)
      ) {
        await this.resetEmbedStyles();
        this.isEmbedVisible = false;
        this.$root.isVisibleVideoContainer = false;
        this.$root.isPinnedContainer = false;
        this.$root.isMoveContainer = false;
        this.$root.containerId = "";
        this.isPinBtnActive = false;

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

    onPinHandler(ev) {
      const deltaHeight = this.$refs.embedWrapper.clientHeight / 2;

      if (this.isPined) {
        this.$refs.embedWrapper.style.position = "absolute";

        this.$refs.embedWrapper.style.top = ev.pageY + (deltaHeight - 7) + "px";

        this.isPined = false;
        this.isPinBtnActive = false;
        this.$root.isPinnedContainer = false;
        return;
      }

      const top =
        this.$refs.embedWrapper.getBoundingClientRect().top +
        deltaHeight +
        "px";

      const left = this.$refs.embedWrapper.getBoundingClientRect().left + "px";

      this.$refs.embedWrapper.style.position = "fixed";

      this.$refs.embedWrapper.style.top = top;
      this.$refs.embedWrapper.style.left = left;

      this.isPined = true;
      this.isPinBtnActive = true;
      this.$root.isPinnedContainer = true;
    },

    onMouseDownHandler(ev) {
      if (this.isPined) {
        this.isMoveBtnActive = false;
        this.$root.isMoveContainer = false;
        return;
      }

      this.isMoveBtnActive = true;
      this.$root.isMoveContainer = true;

      const deltaHeight = this.$refs.embedWrapper.clientHeight / 2;

      let shiftX =
        ev.clientX - this.$refs.embedWrapper.getBoundingClientRect().left;
      let shiftY =
        ev.clientY -
        this.$refs.embedWrapper.getBoundingClientRect().top -
        deltaHeight;

      const moveAt = (pageX, pageY) => {
        this.$refs.embedWrapper.style.left = pageX - shiftX + "px";
        this.$refs.embedWrapper.style.top = pageY - shiftY + "px";
      };

      moveAt(ev.pageX, ev.pageY);

      function onMouseMove(ev) {
        moveAt(ev.pageX, ev.pageY);
      }

      document.addEventListener("mousemove", onMouseMove);

      this.$refs.embedWrapper.onmouseup = function () {
        document.removeEventListener("mousemove", onMouseMove);
        this.onmouseup = null;
      };
    },

    async resetEmbedStyles() {
      // use the condition below for the Offline embed containers to auto-play and dont get closed
      // if (this.showOnline) {
      // }
      this.isPined = false;
      if (this.$refs.itemWrapper) {
        const rootWidth = this.$refs.itemWrapper.offsetWidth;
        const scaleSize = rootWidth / this.embedWidth;
        if (this.$refs.embedWrapper != undefined) {
          this.$refs.embedWrapper.style.position = "absolute";
          this.$refs.embedWrapper.style.transformOrigin = "left center";
          this.$refs.embedWrapper.style.transform = `translateY(-50%) scale(${scaleSize})`;
          this.$refs.embedWrapper.style.opacity = "0";
        }
      }
    },

    setEmbedSizes() {
      this.embedWidth = window.innerWidth > 1279 ? 400 : 355;
      this.embedHeight = window.innerWidth > 1279 ? 350 : 311;
    },

    startDragging(e) {
      if (this.$root.isMoveContainer) {
        return;
      }
      this.mouseDown = true;
      this.startX = e.pageX - this.$refs.channelBox.offsetLeft;
      this.scrollLeft = this.$refs.channelBox.scrollLeft;
      this.triggerDragging(e);
    },

    stopDragging(e) {
      this.mouseDown = false;
    },

    triggerDragging(e) {
      e.preventDefault();

      if (this.$root.isMoveContainer) {
        return;
      }

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
        width: this.embedWidth + "px",
        height: this.embedHeight + "px",
      };
    },
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
