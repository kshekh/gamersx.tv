import { ref } from "vue";
import { useContainerStore } from "../stores/containerStore";
import { useVideoStore } from "../stores/VideoStore";

const startX = ref(0); // Starting X position for dragging
const embedWidth = ref(0); // Width of the embedded content
const scrollLeft = ref(0); // Initial scroll position for dragging
const embedHeight = ref(0); // Height of the embedded content

const isPinned = ref(false); // Whether the container is pinned
const mouseDown = ref(false); // Whether the mouse is pressed down
const isCursorHere = ref(false); // Whether the cursor is over the container
const isEmbedVisible = ref(false); // Whether the embed is visible

const isPinBtnActive = ref(false); // Whether the pin button is active
const isMoveBtnActive = ref(false); // Whether the move button is active

const position = ref({ top: "", left: "" }); // Position of the container

const containerStore = useContainerStore();
const videoStore = useVideoStore();

function clickContainer(elementId, isFullWidth = false) {
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

  /*
   *
   *
   * NOTE: isCursorHere and isVisibleVideoContainer are always true because we
   * set their values to true in this method.
   */
  if (this.isCursorHere && this.containerStore.isVisibleVideoContainer) {
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

  function closeContainer(isButtonClicked) {
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

  async function hideVideo(elementId) {
    if (!containerStore.isPinnedContainer) {
      this.resetEmbedStyles();
    }

    isEmbedVisible.value = false;
    isPinBtnActive.value = false;
    containerStore.isMoveContainer = false;
    containerStore.isVisibleVideoContainer = false;

    this.stopCurrentPlayer();
  }

  function setEmbedPosition(isFullWidth) {
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

  function setContainerStyles() {
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

  function resetContainerStyles() {
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

  /**
   * Stops the current player if there is any
   */
  function stopCurrentPlayer() {
    if (videoStore.currentEmbed && videoStore.currentEmbed.isPlaying()) {
      videoStore.currentEmbed.stopPlayer();
    }
  }
}
