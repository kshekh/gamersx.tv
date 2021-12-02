<template>
  <div class="w-full h-full flex flex-col">
    <div
      class="
        cut-edge__wrapper
        flex-grow
        min-h-0
        w-36
        md:w-86
        xl:w-118
        relative
        cut-edge__wrapper--twitch
      "
    >
      <div
        class="
          cut-edge__clipped
          cut-edge__clipped--sm-border
          cut-edge__clipped-top-right-md
          h-full
          bg-black
          overflow-hidden
          cut-edge__clipped--twitch
        "
      >
        <img
          v-if="showArt && image"
          :src="image.url"
          class="-translate-y-1/2 relative top-1/2 transform w-full"
        />

        <img
          v-else-if="showOverlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="-translate-y-1/2 relative top-1/2 transform w-full"
        />
        <a
          :href="link"
          class="
            absolute
            top-1/2
            left-1/2
            transform
            -translate-x-1/2 -translate-y-1/2
            z-10
            h-12
            md:h-16
            xl:h-32
            w-12
            md:w-16
            xl:w-32
            rounded-full
          "
          target="_blank"
        >
          <play-button
            buttonClass="w-full h-full"
            svgClass="w-3 md:w-7 xl:w-12"
            wrapperClass="md:pl-1.5 xl:pl-3"
            videoType="twitch"
          />
        </a>
      </div>
    </div>
  </div>
</template>
<script>
import PlayButton from "../../helpers/PlayButton.vue";

export default {
  name: "NoEmbedContainerImagery",
  components: {
    "play-button": PlayButton,
  },
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
