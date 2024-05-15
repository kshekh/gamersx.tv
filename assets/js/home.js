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
  },
});
