<template>
  <div @mouseenter="mouseEntered" @mouseleave="mouseLeft" class="w-full h-full">
    <div class="max-w-1/3 relative z-10">
      <!-- "h-52", "bg-purple" temp -->
      <div class="mb-1 md:mb-2 h-52 bg-purple">
        <img
          v-if="showArt"
          :src="image.url"
          class="max-h-20 md:max-h-28 xl:max-h-52"
        />

        <img
          v-else-if="overlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="max-h-20 md:max-h-28 xl:max-h-52"
        />
      </div>

      <p class="mb-2 xl:mb-4 text-white text-xs md:text-sm xl:text-lg">
        <!-- {{ info.description }} -->
        <!-- Will be "description" field -->
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam, libero eos
        neque cum explicabo expedita consectetur quibusdam odio molestiae esse
        voluptate magni, possimus hic eius sapiente quasi iste laborum?
        Incidunt.
      </p>

      <div class="space-x-2 md:space-x-3 xl:space-x-4">
        <button
          @click="playVideo()"
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
    <div v-if="showEmbed && embedData">
      <div v-show="isEmbedVisible">
        <component
          ref="embed"
          :is="embedName"
          :embedData="embedData"
          class="flex-grow min-h-0 absolute inset-0"
          :width="'100%'"
          :height="'100%'"
        ></component>
      </div>
    </div>

    <!-- If there's no embed, show that instead with a link first -->
    <div v-else-if="showArt && image" class="w-full h-full absolute inset-0">
      <a :href="link" class="block w-full h-full">
        <img :src="image.url" class="w-full h-full" />
      </a>
    </div>

    <!-- If there's only an overlay and isn't art, show that instead with a link -->
    <div v-else-if="showOverlay" class="w-full h-full absolute inset-0">
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
import TwitchEmbed from "../../embeds/TwitchEmbedFullWidth.vue";
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
    playVideo() {
      this.$refs.embed.startPlayer();
    },
    mouseEntered() {
      setTimeout(() => {
        if (this.showOverlay || this.showArt) {
          this.isOverlayVisible = false;
          this.isEmbedVisible = true;
        }
      }, 0);

      this.playVideo();
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
