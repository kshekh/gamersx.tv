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
    resetAllExceptContainerId() {
      this.currentVisibleContainerPositionX = "";
      this.currentVisibleContainerPositionY = "";
      this.embedRef = "";
      this.isMoveContainer = false;
      this.isPinBtnActive = false;
      this.isPinned = false;
      this.isPinnedContainer = false;
      this.isVisibleVideoContainer = false;
      this.startParentPosition = {
        Y: "",
        X: "",
      };
      this.bottomRightPosition = {
        Y: "",
        X: "",
      };
      this.pinnedPosition = {
        Y: "",
        X: "",
      };
      this.unPinnedPosition = {
        Y: "",
        X: "",
      };
      this.lastMovedPosition = {
        Y: "",
        X: "",
      };
      this.currentPosition = {
        Y: "",
        X: "",
      };
      this.positionBeforeUnpin = {
        top: "",
        left: "",
      };
      this.previousPinnedPosition = {
        y: "",
        x: "",
      };
    },
  },
});
