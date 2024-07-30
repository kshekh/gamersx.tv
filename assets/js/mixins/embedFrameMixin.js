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

      position: { top: "", left: "" },
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

    clickContainer(elementId, isFullWidth) {
      if (this.$root.containerId === elementId) {
        return;
      }
      console.log(isFullWidth);
      if (!!this.$root.embedRef && this.$root.isPinnedContainer) {
        const prevVideoContainer = this.$root.embedRef;
        const videoContainerPosition =
          prevVideoContainer.getBoundingClientRect();

        this.position = {
          top: isFullWidth
            ? videoContainerPosition.top + window.scrollY
            : videoContainerPosition.top + window.scrollY,
          left: videoContainerPosition.left,
        };
      }

      this.closeContainer();

      if (this.$root.containerId) {
        this.$root.$emit("close-other-layouts", this.$root.containerId);
      }

      this.$root.containerId = elementId;
      this.$root.embedRef = this.$refs.embedWrapper;
      this.isCursorHere = true;
      this.$root.isVisibleVideoContainer = true;

      if (this.isCursorHere && this.$root.isVisibleVideoContainer) {
        setTimeout(() => {
          this.setEmbedPosition(isFullWidth);
          this.$root.$emit("close-other-layouts", this.$root.containerId);
          this.position = { top: "", left: "" };
          this.$root.isPinnedContainer = false;
          this.isEmbedVisible = true;
          if (this.$refs.embed) this.$refs.embed.startPlayer();
        }, 30);
      }
    },

    mouseLeave() {
      this.isCursorHere = false;
    },

    closeContainer(isButtonClick) {
      if (isButtonClick) {
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
        return;
      }
      this.isPined = false;
      this.isPinBtnActive = false;
      this.$root.isVisibleVideoContainer = false;
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
        if (!this.$root.isPinnedContainer) {
          this.resetEmbedStyles();
        }
        this.isEmbedVisible = false;
        this.$root.isVisibleVideoContainer = false;
        // this.$root.isPinnedContainer = false;
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

    setEmbedPosition(isFullWidth) {
      if (this.$root.isPinnedContainer && !!this.position.top) {
        const videoContainer = this.$refs.embedWrapper;
        videoContainer.style.top = this.position.top + "px";
        videoContainer.style.left = this.position.left + "px";
        videoContainer.style.opacity = "1";
        return;
      }
      const videoContainer = this.$refs.embedWrapper;
      const videoContainerPosition = videoContainer.getBoundingClientRect();
      const viewportHeight = document.documentElement.clientHeight;
      const viewportWidth = document.documentElement.clientWidth;

      let resultY = isFullWidth
        ? viewportHeight -
          videoContainerPosition.height -
          50 -
          videoContainerPosition.top +
          600
        : viewportHeight -
          videoContainerPosition.height -
          50 -
          videoContainerPosition.top;

      resultY -=30;

      const resultX =
        viewportWidth -
        videoContainerPosition.width -
        50 -
        videoContainerPosition.left;

      videoContainer.style.transform = `translateY(${resultY}px) translateX(${resultX}px)`;
      videoContainer.style.opacity = "1";
    },

    onPinHandler(ev, isFullWidth) {
      const container = this.$refs.embedWrapper;
      const top = Math.abs(container.getBoundingClientRect().top);
      const left = container.getBoundingClientRect().left;

      if (this.isPined) {
        this.position = { top: "", left: "" };
        container.style.transition = "none";
        container.style.position = "absolute";

        container.style.top = isFullWidth
          ? top + window.scrollY + "px"
          : top + window.scrollY + "px";
        container.style.left = left + "px";

        this.isPined = false;
        this.isPinBtnActive = false;
        this.$root.isPinnedContainer = false;
        return;
      }

      container.style.transition = "none";
      container.style.transform = "none";
      container.style.position = "fixed";

      container.style.top = isFullWidth ? top + "px" : top + "px";
      container.style.left = left + "px";

      this.isPined = true;
      this.isPinBtnActive = true;
      this.$root.isPinnedContainer = true;
    },

    onMouseDownHandler(ev, isFullWidth) {
      this.$refs.embedWrapper.style.transition = "none";
      if (this.isPined) {
        this.isMoveBtnActive = false;
        this.$root.isMoveContainer = false;
        return;
      }

      this.isMoveBtnActive = true;
      this.$root.isMoveContainer = true;

      let shiftX =
        ev.clientX - this.$refs.embedWrapper.getBoundingClientRect().left;

      const moveAt = (pageX, pageY) => {
        this.$refs.embedWrapper.style.transform = "none";
        this.$refs.embedWrapper.style.left = pageX - shiftX + "px";
        this.$refs.embedWrapper.style.top = isFullWidth
          ? pageY - 115 + "px"
          : pageY - 15 + "px";
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

    resetEmbedStyles() {
      if (this.$refs.itemWrapper) {
        if (
          this.$root.isVisibleVideoContainer === false &&
          this.$refs.embedWrapper !== undefined
        ) {
          const container = this.$refs.embedWrapper;
          container.style.position = "absolute";
          container.style.opacity = "0";
          container.style.transform = "none";
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
