import { defineStore } from "pinia";

export const useContainerStore = defineStore("container", {
  state: () => ({
    activeContainerId: null,
    containerId: "",
    currentVisibleContainerPositionX: "",
    currentVisibleContainerPositionY: "",
    embedRef: "",
    isMoveContainer: false,
    isPinBtnActive: false,
    isPinned: false,
    isPinnedContainer: false,
    isVisibleVideoContainer: false,
    startParentPosition: {
      Y: "",
      X: "",
    },
    bottomRightPosition: {
      Y: "",
      X: "",
    },
    pinnedPosition: {
      Y: "",
      X: "",
    },
    unPinnedPosition: {
      Y: "",
      X: "",
    },
    lastMovedPosition: {
      Y: "",
      X: "",
    },
    currentPosition: {
      Y: "",
      X: "",
    },
    positionBeforeUnpin: {
      top: "",
      left: "",
    },
    previousPinnedPosition: {
      y: "",
      x: "",
    },
  }),
  actions: {
    setContainerId(containerId) {
      this.containerId = containerId;
    },
    clearContainerId() {
      this.containerId = null;
    },
  },
});
