export function useContainer() {
  function toggleContainer(elementId, isFullWidth = false) {
    /**
     * If the there is already a container with the same ID, do nothing
     */
    if (containerStore.containerId === elementId) {
      return;
    }

    if (this.containerStore.embedRef && this.containerStore.isPinnedContainer) {
      const prevContainer = containerStore.embedRef;
      const prevContainerPosition = prevContainer.getBoundingClientRect();

      position.value = {
        top: isFullWidth
          ? prevContainerPosition.top + window.scrollY
          : prevContainerPosition.top + window.scrollY,
        left: prevContainerPosition.left,
      };
    }

    closeCurrentContainer();

    /*
     * This method hide whatever container is currently running.
     */
    if (this.videoStore.currentVideoEmbed) {
      videoStore.hideVideo();
    }

    isCursorHere.value = true; // only set as "true" here
    containerStore.containerId = elementId;
    containerStore.embedRef = this.$refs.embedWrapper;
    containerStore.isVisibleVideoContainer = true; // only set as "true" here

    setTimeout(() => {
      position.value = { top: "", left: "" };

      containerStore.isPinnedContainer = false;
      isEmbedVisible.value = false;
      isShowTwitchEmbed.value = false; // What is this ???

      if (videoStore.currentEmbed) {
        videoStore.currentEmbed.startPlayer();
      }
    }, 30);
  }

  function setStyles() {
    setTimeout(() => {
      const bodyRef = document.body.querySelectorAll(".common-container__body");
      for (let i = 0; i < bodyRef.length; i++) {
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
  }

  function resetStyles() {
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
  }

  function closeCurrentContainer(isButtonClicked) {
    isEmbedVisible.value = false;
    isPinned.value = false;
    isPinBtnActive.value = false;
    containerStore.containerId = "";
    containerStore.isPinnedContainer = false;
    containerStore.isVisibleVideoContainer = false;

    if (isButtonClicked) {
      this.stopCurrentPlayer();
    }
  }

  function unmountContainer(embedId) {
    const parentContainer = document.getElementById(embedId);
    if (parentContainer) {
      parentContainer.remove();
    }
  }
}
