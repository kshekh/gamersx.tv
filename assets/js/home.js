import Vue from "vue";
import Home from "./components/Home";

/**
 * * Create a fresh Vue Application instance
 * */
new Vue({
  el: "#app-home",
  components: { Home },
  created: function () {
    this.isVisibleVideoContainer = false;
    this.containerId = '';
    this.isPinnedContainer = false;
    this.isMoveContainer = false;
    this.embedRef = '';
    this.currentVisibleContainerPositionY = '',
    this.currentVisibleContainerPositionX = '',

    this.startParentPosition = {
      Y: '',
      X: ''
    },

    this.bottomRightPosition = {
      Y: '',
      X: ''
    },

    this.pinnedPosition = {
      Y: '',
      X: ''
    },

    this.unPinnedPosition = {
      Y: '',
      X: ''
    },

    this.lastMovedPosition = {
      Y: '',
      X: ''
    },

    this.currentPosition = {
      Y: '',
      X: ''
    },

    this.positionBeforeUnpin = {
      top: '',
      left: ''
  },

  this.previousPinnedPosition = {
    y: '',
    x: ''
  }
  },
});

