<template>
  <div
    class="
      absolute
      left-1/3
      md:left-1/5
      w-22
      md:w-12
      xl:w-22
      h-48
      md:h-26
      xl:h-48
    "
  >
    <div
      class="
        relative
        top-1/2
        -translate-y-1/2
        left-0
        w-full
        h-full
        cut-edge__wrapper
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
          cut-edge__clipped--sm-border cut-edge__clipped--twitch
          bg-black
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
  </div>
</template>
<script>
export default {
  name: "NoEmbedContainerParallax",
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
