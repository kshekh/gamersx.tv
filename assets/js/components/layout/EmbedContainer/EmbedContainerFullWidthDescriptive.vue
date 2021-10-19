<template>
  <div class="w-full h-full">
    <div
      class="
        relative
        z-10
        min-h-mobile-description
        flex flex-col
        opacity-1
        transform
        rounded-md
        transition-all
        duration-300
        backdrop-filter backdrop-blur
        p-3
        -m-3
      "
      :class="[
        isVideoBuffered ? 'md:max-w-1/4' : 'max-w-1/2 md:max-w-1/3',
        { 'opacity-0 translate-y-3': isVideoPlaying },
      ]"
    >
      <div
        class="overflow-hidden transition-all duration-300"
        :class="[
          isVideoBuffered
            ? 'md:mb-1 h-11 md:h-20 xl:h-41'
            : 'mb-1 md:mb-2 h-14 md:h-26 xl:h-52',
        ]"
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
        class="text-white transition-all duration-300"
        :class="[
          isVideoBuffered
            ? 'text-8 md:text-xs xl:text-sm mb-1 xl:mb-2'
            : 'text-xs md:text-sm xl:text-lg mb-2 xl:mb-4',
        ]"
      >
        <!-- Will be "description" field -->
        <!-- {{ info.description }} -->
        League of Legends is a multiplayer online battle arena (MOBA) game in
        which the player controls a character ("champion") with a set of unique
        abilities from an isometric perspective.
      </p>

      <div
        class="mt-auto transition-all duration-300"
        :class="[
          isVideoBuffered
            ? 'space-x-1 md:space-x-2 xl:space-x-3'
            : 'space-x-2 md:space-x-3 xl:space-x-4',
        ]"
      >
        <button
          @click="handlePlayVideo()"
          class="
            text-white
            p-1
            transition-all
            duration-300
            bg-opacity-30
            hover:bg-opacity-100
          "
          :class="[
            bgColor,
            isVideoBuffered
              ? 'text-8 md:text-xs xl:text-sm md:px-1 md:py-1 xl:py-2 xl:px-4 min-w-40 md:min-w-50 xl:min-w-75'
              : 'text-xs md:text-sm xl:text-lg min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6',
          ]"
        >
          Play
        </button>
        <button
          @click="isVideoBuffered = !isVideoBuffered"
          class="
            text-white
            p-1
            transition-all
            duration-300
            bg-opacity-30
            hover:bg-opacity-100
          "
          :class="[
            bgColor,
            isVideoBuffered
              ? 'text-8 md:text-xs xl:text-sm md:px-1 md:py-1 xl:py-2 xl:px-4 min-w-40 md:min-w-50 xl:min-w-75'
              : 'text-xs md:text-sm xl:text-lg min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6',
          ]"
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
          @video-buffered="videoBuffered"
          class="flex-grow min-h-0 absolute inset-0"
          :width="'100%'"
          :height="'100%'"
        ></component>
      </div>
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
    "embedName",
    "embedData",
    "liveViewerCount",
    "isAllowPlaying",
  ],
  data() {
    return {
      isOverlayVisible: true,
      isEmbedVisible: false,
      isTitleVisible: false,
      isVideoPlaying: false,
      isVideoBuffered: false,
    };
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
    },
  },
  methods: {
    handlePlayVideo() {
      this.isVideoPlaying = true;

      this.playVideo();
    },
    playVideo() {
      this.$refs.embed.startPlayer();
    },
    videoBuffered() {
      this.isVideoBuffered = true;
    },
  },
  watch: {
    isAllowPlaying(newVal, oldVal) {
      if (newVal === true) {
        if (this.showOverlay || this.showArt) {
          this.isOverlayVisible = false;
          this.isEmbedVisible = true;
        }

        this.playVideo();
      } else {
        if (this.$refs.embed.isPlaying()) {
          this.isVideoPlaying = false;
          this.$refs.embed.stopPlayer();
        }
      }
    },
  },
  mounted() {
    this.isOverlayVisible = this.showOverlay;
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  },
};
</script>
