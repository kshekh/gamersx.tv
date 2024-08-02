/*
*********Description*********
The provided code defines a Vue.js component that manages the display, interaction,
and behavior of embedded video content, such as Twitch or YouTube videos, within a web page.
The component maintains various states, such as the position, size, visibility,
and interaction status of the embed container. It allows users to pin, move,
and drag the video container, providing a responsive and interactive experience.

When the mouse enters the container, it triggers the display and starts the
video player if necessary. Clicking on the container sets its position
and ensures only one embed is visible at a time. The component includes methods
for handling mouse events (like dragging and dropping), pinning/unpinning the video
container, and adjusting styles based on user interactions and viewport changes.
The computed properties dynamically determine the display attributes, such as the
embed size and button colors, based on the video type and display settings.
Lifecycle hooks manage event listeners for window resizing and custom events to
maintain the component’s behavior and appearance dynamically.

*********Properties*********

--------Refs ($refs)--------
channelBox: div element used for displaying channels. Used in components/front
embed: refers to the actual Twitch or YouTube embed. Used in layout/EmbedContainer
embedWrapper: div element that wraps around the actual embed element. Used in components/front

--------Root ($root)--------
containerId
embedRef
isMoveContainer
isPinnedContainer
isVisibleVideoContainer
*/
export default {
  // Data properties for the component
  data() {
    return {
      startX: 0, // Starting X position for dragging
      embedWidth: 0, // Width of the embedded content
      scrollLeft: 0, // Initial scroll position for dragging
      embedHeight: 0, // Height of the embedded content

      isPinned: false, // Whether the container is pinned
      mouseDown: false, // Whether the mouse is pressed down
      isCursorHere: false, // Whether the cursor is over the container
      isEmbedVisible: false, // Whether the embed is visible

      isPinBtnActive: false, // Whether the pin button is active
      isMoveBtnActive: false, // Whether the move button is active

      position: { top: "", left: "" }, // Position of the container
    };
  },

  methods: {
    // Method called when the mouse enters the container
    // mouseEntered() {
    //   if (this.$root.isVisibleVideoContainer) {
    //     return;
    //   }
    //   console.log('The mouse enters!');
    //   this.isCursorHere = true;
    //   this.$root.isVisibleVideoContainer = true;
    //   if (this.isCursorHere && this.$root.isVisibleVideoContainer) {
    //     setTimeout(() => {
    //       this.setEmbedPosition();
    //       this.$root.$emit("close-other-layouts", this.embedData.elementId);
    //
    //       if (this.$refs.embed) this.$refs.embed.startPlayer();
    //     }, 30);
    //   }
    // },

    /*
    * Method called when the container is clicked.
    *
    * Called by: All embed containers except FullWidthImagery.
    */
    clickContainer(elementId, isFullWidth) {
      if (this.$root.containerId === elementId) {
        return;
      }
      console.log('container ID', this.$root.containerId);
      console.log('element ID', elementId)
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
      this.isCursorHere = true; // only set as "true" here
      this.$root.isVisibleVideoContainer = true; // only set as "true" here
      console.log('container ID', this.$root.containerId)

      // isCursorHere is always true because we just set it as true
      if (this.isCursorHere && this.$root.isVisibleVideoContainer) {
        setTimeout(() => {
          this.setEmbedPosition(isFullWidth);

          this.$root.$emit("close-other-layouts", this.$root.containerId);

          this.position = { top: "", left: "" };
          this.$root.isPinnedContainer = false;
          this.isEmbedVisible = true;

          if (this.$refs.embed) {
            this.$refs.embed.startPlayer()
          };
        }, 30);
      }
    },

    // Method called when the mouse leaves the container
    // mouseLeave() {
    //   this.isCursorHere = false;
    // },

    // Method to close the container
    closeContainer(isButtonClick) {
      this.isPinned = false;
      this.isPinBtnActive = false;
      this.$root.isVisibleVideoContainer = false;
      this.$root.containerId = "";

      this.resetEmbedStyles();

      this.isEmbedVisible = false;

      if (isButtonClick) {
        this.$root.isPinnedContainer = false;
        console.log('I reset on close when button clicked');

        if (this.$refs.embed && this.$refs.embed.isPlaying()) {
          this.$refs.embed.stopPlayer();
        }
        return;
      }

      console.log('I reset on close just cause');

      if (this.$refs.embed && this.$refs.embed.isPlaying()) {
        this.$refs.embed.stopPlayer();
      }
    },

    // Method to hide the video
    async hideVideo(elementId) {
      console.log('element ID (hide): ', elementId);
      console.log('element ID (hide) embed: ', this.embedData);
      console.trace();
      console.log('I should hide: ', !(elementId && this.embedData && this.embedData.elementId === elementId));
      // Use the condition below for the offline embed containers to auto-play and not get closed
      if (
        !(elementId && this.embedData && this.embedData.elementId === elementId)
      ) {
        if (!this.$root.isPinnedContainer) {
          console.log('I reset on hide');
          this.resetEmbedStyles();
        }
        this.isEmbedVisible = false;
        this.$root.isVisibleVideoContainer = false;
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

    // Method to set the position of the embed
    setEmbedPosition(isFullWidth) {
      /*
      *  Ensures that the video remains in the same position if it is pinned
      */
      if (this.$root.isPinnedContainer && !!this.position.top) {
        const videoContainer = this.$refs.embedWrapper;
        videoContainer.style.top = this.position.top + "px";
        videoContainer.style.left = this.position.left + "px";
        videoContainer.style.opacity = "1";
        return;
      }
      const videoContainer = this.$refs.embedWrapper;
      const videoContainerPosition = videoContainer.getBoundingClientRect();
      // Get the height of the root element
      const viewportHeight = document.documentElement.clientHeight;
      // Get the width of the root element
      const viewportWidth = document.documentElement.clientWidth;

      let containerPositionY = videoContainerPosition.y;
      let offsetY = 40;
      let moveToPositionY = viewportHeight - videoContainerPosition.height - offsetY;
      let translateDistanceY = moveToPositionY - containerPositionY;

      const containerPositionLeft = videoContainerPosition.left; // Current left position of the container
      const containerWidth = videoContainer.clientWidth + 100; // Width of the container
      const offsetX = 40; // Desired distance from the right edge of the viewport
      const targetPositionX = viewportWidth - containerWidth - offsetX; // Target left position
      const translateDistanceX = targetPositionX - containerPositionLeft; // Translation distance

      console.log('container position left', containerPositionLeft)
      // console.log('move to position X', moveToPositionX);
      console.log('translate distance X', translateDistanceX);
      console.log('viewport width', viewportWidth);

      // let resultY = viewportHeight - videoContainerPosition.height - 50;
      // if (isFullWidth) {
      //   resultY = viewportHeight - videoContainerPosition.height - 50 - 10; // Ensure it's at the bottom
      // }
      //
      // const resultX = viewportWidth - videoContainerPosition.width - 140 - 10; // Ensure it's at the right edge
      //
      console.log('container position', videoContainerPosition);
      // console.log(resultY, resultX);
      // // 350 400
      console.log('container dimensions', videoContainer.clientHeight, videoContainer.clientWidth);
      // 572 1420
      console.log('document dimensions', document.documentElement.clientHeight, document.documentElement.clientWidth)
      console.log('Embed is visible', this.isEmbedVisible);
      videoContainer.style.transform = `translateY(${translateDistanceY}px) translateX(${translateDistanceX}px)`;
      videoContainer.style.opacity = "1";
    },

    // Method to handle pinning the container
    onPinHandler(ev, isFullWidth) {
      const container = this.$refs.embedWrapper;
      const top = Math.abs(container.getBoundingClientRect().top);
      const left = container.getBoundingClientRect().left;

      if (this.isPinned) {
        this.position = { top: "", left: "" };
        container.style.transition = "none";
        container.style.position = "absolute";

        container.style.top = isFullWidth
          ? top + window.scrollY + "px"
          : top + window.scrollY + "px";
        container.style.left = left + "px";

        this.isPinned = false;
        this.isPinBtnActive = false;
        this.$root.isPinnedContainer = false;
        return;
      }

      container.style.transition = "none";
      container.style.transform = "none";
      container.style.position = "fixed";

      container.style.top = isFullWidth ? top + "px" : top + "px";
      container.style.left = left + "px";

      this.isPinned = true;
      this.isPinBtnActive = true;
      this.$root.isPinnedContainer = true;
    },

    // Method to handle mouse down event for dragging
    onMouseDownHandler(ev, isFullWidth) {
      this.$refs.embedWrapper.style.transition = "none";
      if (this.isPinned) {
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

    /*
    *  The following method sets embed position to absolute and
    *  resets the opacity and transform.
    *
    *  Note: The method only runs when there is an itemWrapper ref which
    *  is used by all embed containers except for EmbedContainerFullWidthDescriptive
    */
    resetEmbedStyles() {
      console.log('I reset');
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

    // Method to set the embed sizes
    setEmbedSizes() {
      this.embedWidth = window.innerWidth > 1279 ? 400 : 355;
      this.embedHeight = window.innerWidth > 1279 ? 350 : 311;
    },

    // Method to start dragging
    startDragging(e) {
      if (this.$root.isMoveContainer) {
        return;
      }
      this.mouseDown = true;
      this.startX = e.pageX - this.$refs.channelBox.offsetLeft;
      this.scrollLeft = this.$refs.channelBox.scrollLeft;
      this.triggerDragging(e);
    },

    // Method to stop dragging
    stopDragging(e) {
      this.mouseDown = false;
    },

    // Method to handle dragging
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

  // Computed properties for the component
  computed: {
    // Computed property for the play button color
    playBtnColor() {
      return this.embedName === "TwitchEmbed" ? "twitch" : "youtube";
    },

    // Computed property to determine if the embed should be shown
    showEmbed() {
      return (
        (this.showOnline && this.onlineDisplay.showEmbed) ||
        (!this.showOnline && this.offlineDisplay.showEmbed)
      );
    },

    // Computed property to determine if the art should be shown
    showArt() {
      return (
        (this.showOnline && this.onlineDisplay.showArt) ||
        (!this.showOnline && this.offlineDisplay.showArt)
      );
    },

    // Computed property to determine if the overlay should be shown
    showOverlay() {
      return (
        this.overlay &&
        ((this.showOnline && this.onlineDisplay.showOverlay) ||
          (!this.showOnline && this.offlineDisplay.showOverlay))
      );
    },

    // Computed property for the embed size
    embedSize() {
      return {
        width: this.embedWidth + "px",
        height: this.embedHeight + "px",
      };
    },
  },

  // Lifecycle hooks
  created() {
    // Add event listener for window resize
    window.addEventListener("resize", this.setEmbedSizes);
    this.setEmbedSizes();
  },

  mounted() {
    // Listen for the "close-other-layouts" event
    this.$root.$on("close-other-layouts", this.hideVideo);
    console.log('I reset on mount');
    this.resetEmbedStyles();
  },

  destroyed() {
    // Remove the "close-other-layouts" event listener and window resize listener
    this.$root.$off("close-other-layouts", this.hideVideo);
    window.removeEventListener("resize", this.setEmbedSizes);
  },
};