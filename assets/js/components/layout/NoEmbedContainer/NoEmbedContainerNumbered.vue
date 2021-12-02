<template>
  <div
    class="
      cut-edge__wrapper
      flex
      items-center
      w-18
      h-24
      md:w-24
      md:h-32
      xl:w-28
      xl:h-40
      flex-shrink-0
      z-10
      relative
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
        cut-edge__clipped-top-left-sm
        bg-black
        flex-shrink-0
        cut-edge__clipped--twitch
        overflow-hidden
      "
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
  name: "NoEmbedContainerNumbered",
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
  ],
  data: function () {
    return {};
  },
  computed: {
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
