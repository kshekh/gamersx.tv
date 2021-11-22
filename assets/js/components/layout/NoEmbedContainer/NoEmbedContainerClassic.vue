<template>
  <div
    class="
      cut-edge__wrapper
      w-full
      h-full
      flex-shrink-0
      cut-edge__wrapper--twitch
    "
  >
    <a
      :href="link"
      class="
        block
        w-full
        h-full
        cut-edge__clipped
        cut-edge__clipped--sm-border
        bg-black
        cut-edge__clipped--twitch
        overflow-hidden
      "
      :class="borderClass"
      target="_blank"
    >
      <img
        v-if="showArt && image"
        :src="image.url"
        class="relative top-1/2 transform -translate-y-1/2 w-full"
      />
      <img
        v-else-if="showOverlay"
        alt="Embed's Custom Overlay"
        :src="overlay"
        class="relative top-1/2 transform -translate-y-1/2 w-full"
      />
    </a>
  </div>
</template>
<script>
export default {
  name: "NoEmbedContainerClassic",
  props: [
    "title",
    "channelName",
    "showOnline",
    "onlineDisplay",
    "offlineDisplay",
    "rowName",
    "image",
    "overlay",
    "link",
    "componentName",
    "embedName",
    "embedData",
    "cuttedBorder"
  ],
  data: function () {
    return {
    };
  },
  computed: {
    borderClass() {
      return this.cuttedBorder ? 'cut-edge__clipped-top-left-sm' : '';
    },
    showArt: function () {
      return (
        (this.showOnline && this.onlineDisplay.showArt) ||
        (!this.showOnline && this.offlineDisplay.showArt)
      );
    },
    showOverlay: function () {
      return (
        this.overlay &&
        ((this.showOnline && this.onlineDisplay.showOverlay) ||
          (!this.showOnline && this.offlineDisplay.showOverlay))
      );
    },
  },
};
</script>
