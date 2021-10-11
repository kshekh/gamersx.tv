<template>
  <div
    class="
        w-full
        h-full
      "
    @mouseenter="isTitleVisible = true"
    @mouseleave="isTitleVisible = false"
  >
    <div
      class="absolute inset-0 z-negative bg-cover bg-no-repeat"
      :style="
        `background-image: url(https://static-cdn.jtvnw.net/jtv_user_pictures/3df86ad6-a622-4091-8fa7-78e19a2eb5ed-channel_offline_image-1920x1080.png);`
      "
    ></div>
    <!-- :style="`background-image: url(${customArt});`" -->
    <div v-show="isOverlayVisible" class="max-w-1/3">
      <div class="mb-1 md:mb-2">
        <img v-if="showArt" :src="image.url" class="w-full h-full" />

        <img
          v-else-if="overlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="w-full h-full"
        />
      </div>

      <p class="mb-2 xl:mb-4 text-white text-xs md:text-sm xl:text-lg">
        {{ info.description }}
      </p>

      <div class="space-x-2 md:space-x-3 xl:space-x-4">
        <button
          class="text-white text-xs md:text-sm xl:text-lg p-1 min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6"
          :class="bgColor"
        >
          Play
        </button>
        <button
          class="text-white text-xs md:text-sm xl:text-lg p-1 min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6"
          :class="bgColor"
        >
          More info
        </button>
      </div>
    </div>

    <!-- Show the embed with overlay if there's an embed -->
    <div
      v-if="showEmbed && embedData"
      class="w-full h-full relative"
      @mouseenter="mouseEntered"
      @mouseleave="mouseLeft"
    >
      <div v-show="isEmbedVisible">
        <component
          ref="embed"
          :is="embedName"
          :embedData="embedData"
          class="flex-grow min-h-0"
          :width="'100%'"
          :height="'100%'"
        ></component>
      </div>
    </div>

    <!-- If there's no embed, show that instead with a link first -->
    <div v-else-if="showArt && image" class="w-full h-full">
      <a :href="link" class="block w-full h-full">
        <img :src="image.url" class="w-full h-full" />
      </a>
    </div>

    <!-- If there's only an overlay and isn't art, show that instead with a link -->
    <div v-else-if="showOverlay" class="w-full h-full">
      <a :href="link" class="block w-full h-full">
        <img
          class="w-full h-full"
          alt="Embed's Custom Overlay"
          :src="overlay"
        />
      </a>
    </div>
  </div>
</template>

<script>
import TwitchEmbed from "../../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

export default {
  name: "EmbedContainerFullWidthDescriptive",
  components: {
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed
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
    "embedName",
    "embedData",
    "liveViewerCount"
  ],
  data() {
    return {
      isOverlayVisible: true,
      isEmbedVisible: false,
      isTitleVisible: false
    };
  },
  methods: {
    mouseEntered() {
      if (this.showOverlay || this.showArt) {
        this.isOverlayVisible = false;
        this.isEmbedVisible = true;
      }
      this.$refs.embed.startPlayer();
    },
    mouseLeft() {
      if (this.showOverlay || this.showArt) {
        this.isOverlayVisible = true;
        this.isEmbedVisible = false;
      }
      if (this.$refs.embed.isPlaying()) {
        this.$refs.embed.stopPlayer();
      }
    }
  },
  computed: {
    bgColor() {
      return this.embedName === "TwitchEmbed" ? "bg-purple" : "bg-red";
    },
    showEmbed() {
      return (
        (this.showOnline && this.onlineDisplay.showEmbed) ||
        (!this.showOnline && this.offlineDisplay.showEmbed)
      );
    },
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
    }
  },
  mounted() {
    this.isOverlayVisible = this.showOverlay;
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  }
};
</script>
