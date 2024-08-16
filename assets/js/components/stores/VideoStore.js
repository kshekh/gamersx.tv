import { defineStore } from "pinia";

export const useVideoStore = defineStore("video", {
  state: () => ({
    activeEmbed: {},
    embeddedObject: {},
    isEmbedVisible: false,
    isHideButtonClicked: false,
    isVideoPlaying: false,
    isPositionSet: false,
  }),
  getters: {
    activeEmbedIsEmpty(state) {
      return Object.keys(state.activeEmbed).length === 0;
    },
  },
  actions: {
    storeEmbed(embed) {
      this.activeEmbed = embed;
    },
    clearExistingEmbed() {
      this.activeEmbed = {};
    },
    hideVideo() {
      this.activeEmbed.stopPlayer();

      this.isEmbedVisible = false;
      this.isHideButtonClicked = false;
    },
    setPosition(videoContainer) {
      /*
       * Ensures that the video remains in the same position if it is pinned
       */
      // if (containerStore.isPinnedContainer && !!position.value.top) {
      //   videoContainer.style.top = this.position.top + "px";
      //   videoContainer.style.left = this.position.left + "px";
      //   videoContainer.style.opacity = 1;

      //   return;
      // }

      const videoContainerPosition = videoContainer.getBoundingClientRect();

      // Get the height and width of the root element
      const viewportHeight = document.documentElement.offsetHeight;
      const viewportWidth = document.documentElement.offsetWidth;

      const offsetX = 40;
      let offsetY = 40;

      let containerPositionY = videoContainerPosition.y;

      let moveToPositionY =
        viewportHeight - videoContainerPosition.height - offsetY;
      let translateDistanceY = moveToPositionY - containerPositionY;

      const containerPositionLeft = videoContainerPosition.left;
      const containerWidth = videoContainer.offsetWidth * 1.25;
      const targetPositionX = viewportWidth - containerWidth - offsetX;
      const translateDistanceX = targetPositionX - containerPositionLeft;

      videoContainer.style["transform-origin"] = "bottom right";
      videoContainer.style.transform = `translateY(${translateDistanceY}px) translateX(${translateDistanceX}px)`;
      videoContainer.style.opacity = 1;

      this.isPositionSet = true;
    },
    setStyles() {
      setTimeout(() => {
        const bodyRef = document.body.querySelectorAll(
          ".common-container__body",
        );
        for (let i = 0; i < bodyRef.length; i++) {
          console.log(`The `, i, ` element is `, bodyRef[i]);
          bodyRef[i].style.background = "#130E1C";
          bodyRef[i].style.outline = "3px solid #7A4ECC";
          bodyRef[i].style.filter =
            "drop-shadow(0 5px 10px rgba(122, 78, 204, 0.5))";
        }

        const actionRef = document.body.querySelectorAll(
          ".common-container__actions",
        );
        for (let i = 0; i < actionRef.length; i++) {
          actionRef[i].style.opacity = 1;
          actionRef[i].style.filter =
            "drop-shadow(0 5px 10px rgba(122, 78, 204, 0.5))";
        }
      }, 2000);
    },
    resetStyles() {
      const bodyRef = document.body.querySelectorAll(".common-container__body");
      for (let i = 0; i < bodyRef.length; i++) {
        bodyRef[i].style.background = "none";
        bodyRef[i].style.outline = "none";
        bodyRef[i].style.filter = "none";
      }

      const actionRef = document.body.querySelectorAll(
        ".common-container__actions",
      );
      for (let i = 0; i < actionRef.length; i++) {
        actionRef[i].style.opacity = 0;
        actionRef[i].style.filter = "none";
      }
    },
    resetEmbed(videoContainer) {
      videoContainer.style.position = "absolute";
      videoContainer.style.transform = "none";
      videoContainer.style.opacity = 0;

      this.isPositionSet = false;
    },
    setVideoPlaying() {
      this.isVideoPlaying = true;
    },
    setVideoNotPlaying() {
      this.isVideoPlaying = false;
    },
  },
});
