export function useEmbed() {
  function setDimensions() {
    this.embedWidth = window.innerWidth > 1279 ? 400 : 355;
    this.embedHeight = window.innerWidth > 1279 ? 350 : 311;
  }

  function setPosition() {
    const videoContainer = containerStore.embedWrapper;
    if (!videoContainer) {
      return;
    }

    /*
     * Ensures that the video remains in the same position if it is pinned
     */
    if (containerStore.isPinnedContainer && !!position.value.top) {
      videoContainer.style.top = this.position.top + "px";
      videoContainer.style.left = this.position.left + "px";
      videoContainer.style.opacity = 1;

      return;
    }

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
  }

  function resetEmbed() {
    const videoContainer = containerStore.embedWrapper;
    if (videoContainer) {
      videoContainer.style.position = "absolute";
      videoContainer.style.transform = "none";
      videoContainer.style.opacity = 0;
    }
  }

  function resetEmbedStyles() {
    if (this.$refs.itemWrapper) {
      if (
        this.$refs.embedWrapper !== undefined &&
        this.containerStore.isVisibleVideoContainer === false
      ) {
        const container = this.$refs.embedWrapper;
        container.style.position = "absolute";
        container.style.opacity = 0;
        container.style.transform = "none";
      }
    }
  }
}
