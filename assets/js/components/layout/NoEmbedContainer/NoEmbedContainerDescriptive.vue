<template>
  <div>
    <div
      class="
        relative
        z-10
        flex flex-col
        opacity-1
        transform
        rounded-md
        transition-all
        duration-300
        backdrop-filter backdrop-blur
        px-3
        -mx-3
        max-w-1/2
        md:max-w-1/3
        md:min-w-240
      "
    >
      <div
        class="
          overflow-hidden
          transition-all
          duration-300
          mb-1
          md:mb-2
          h-14
          md:h-26
          xl:h-52
        "
      >
        <img
          v-if="showArt && image"
          :src="image.url"
          class="max-h-20 md:max-h-28 xl:max-h-52"
        />

        <img
          v-else-if="showOverlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="max-h-20 md:max-h-28 xl:max-h-52"
        />
      </div>

      <p
        class="
          text-white
          transition-all
          duration-300
          text-xs
          md:text-sm
          xl:text-lg
          mb-2
          xl:mb-4
        "
      >
         {{ description }}
      </p>

      <div
        class="
          mt-6
          md:mt-auto
          transition-all
          duration-300
          flex
          items-center
          space-x-2
          md:space-x-3
          xl:space-x-4
        "
      >
        <a
          :href="link"
          class="
            inline-block
            text-white
            p-1
            transition-all
            duration-300
            bg-opacity-30
            hover:bg-opacity-100
            text-xs
            md:text-sm
            xl:text-lg
            min-w-50
            md:min-w-75
            xl:min-w-130
            md:px-3
            md:py-2
            xl:py-3
            xl:px-6
            bg-purple
          "
          target="_blank"
        >
          Show More
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import TwitchEmbed from "../../embeds/TwitchEmbedFullWidth.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

export default {
  name: "NoEmbedContainerDescriptive",
  components: {
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed,
  },
  props: [
    "title",
    "info",
    "customArt",
    "channelName",
    "showOnline",
    "onlineDisplay",
    "offlineDisplay",
    "rowName",
    "image",
    "overlay",
    "link",
    "componentName",
    "embedData",
    "description"
  ],
  data() {
    return {};
  },
  computed: {
    showArt() {
      return (
        (this.showOnline && this.onlineDisplay.showArt) ||
        (!this.showOnline && this.offlineDisplay.showArt)
      );
    },
    showOverlay() {
      return (
        this.overlay &&
        ((this.showOnline && this.onlineDisplay.showOverlay) ||
          (!this.showOnline && this.offlineDisplay.showOverlay))
      );
    },
  },
};
</script>
