import { defineStore } from "pinia";

export const useVideoStore = defineStore("video", {
  state: () => ({
    embed: {},
    currentEmbed: {},
    isEmbedVisible: false,
    isHideButtonClicked: false,
  }),
  actions: {
    hideVideo() {
      this.currentEmbed.stopPlayer();

      this.isEmbedVisible = false;
      this.isHideButtonClicked = false;
    },
  },
});
