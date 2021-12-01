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
        duration-700
        backdrop-filter backdrop-blur-xs
        shadow-smooth
        px-3
        -mx-3
      "
      :class="[
        decreaseInfoBoxSize
          ? 'md:max-w-1/4 md:min-w-180'
          : 'max-w-1/2 md:max-w-1/3 md:min-w-240',
        {
          'opacity-0 translate-y-3': isInfoBoxHidden,
          'pointer-events-none': isHideButtonClicked,
        },
      ]"
    >
      <div
        class="overflow-hidden transition-all duration-300"
        :class="[
          decreaseInfoBoxSize
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
          decreaseInfoBoxSize
            ? 'text-8 md:text-xs xl:text-sm mb-1 xl:mb-2'
            : 'text-xs md:text-sm xl:text-lg mb-2 xl:mb-4',
        ]"
      >
         {{ description }}
      </p>

      <div
        class="mt-6 md:mt-auto transition-all duration-300 flex items-center"
        :class="[
          decreaseInfoBoxSize
            ? 'space-x-1 md:space-x-2 xl:space-x-3'
            : 'space-x-2 md:space-x-3 xl:space-x-4',
        ]"
      >
        <button
          v-if="showEmbed && embedData"
          @click.stop="handlePlayVideo()"
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
            decreaseInfoBoxSize
              ? 'text-8 md:text-xs xl:text-sm md:px-1 md:py-1 xl:py-2 xl:px-4 min-w-40 md:min-w-50 xl:min-w-75'
              : 'text-xs md:text-sm xl:text-lg min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6',
          ]"
        >
          Hide
        </button>
        <a
          :href="link"
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
            decreaseInfoBoxSize
              ? 'text-8 md:text-xs xl:text-sm md:px-1 md:py-1 xl:py-2 xl:px-4 min-w-40 md:min-w-50 xl:min-w-75'
              : 'text-xs md:text-sm xl:text-lg min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6',
          ]"
          target="_blank"
        >
          More info
        </a>
        <svg
          v-if="embedName === 'YouTubeEmbed'"
          class="w-3 md:w-6 xl:w-7 flex-shrink-0"
          viewBox="0 0 44 32"
        >
          <linearGradient
            id="PgB_UHa29h0TpFV_moJI9a"
            x1="9.816"
            x2="41.246"
            y1="9.871"
            y2="41.301"
            gradientUnits="userSpaceOnUse"
            gradientTransform="matrix(1, 0, 0, 0.999982, -2, -8.000276)"
          >
            <stop offset="0" stop-color="#f44f5a" />
            <stop offset="0.443" stop-color="#ee3d4a" />
            <stop offset="1" stop-color="#e52030" />
          </linearGradient>
          <path
            fill="#FF0000"
            d="M 43.012 26.56 C 42.573 28.799 40.708 30.506 38.404 30.826 C 34.783 31.36 28.748 32 21.945 32 C 15.252 32 9.217 31.36 5.486 30.826 C 3.182 30.506 1.316 28.799 0.878 26.56 C 0.439 24.107 0 20.48 0 16 C 0 11.52 0.439 7.893 0.878 5.44 C 1.317 3.2 3.182 1.493 5.486 1.172 C 9.107 0.64 15.142 0 21.945 0 C 28.748 0 34.673 0.64 38.404 1.172 C 40.708 1.493 42.574 3.2 43.012 5.44 C 43.451 7.893 44 11.52 44 16 C 43.89 20.48 43.451 24.107 43.012 26.56 Z"
            bx:origin="0.5 0.499994"
          />
          <path
            d="M 30.068 14.44 L 18.632 6.816 C 18.055 6.431 17.318 6.395 16.707 6.723 C 16.096 7.05 15.716 7.683 15.716 8.376 L 15.716 23.624 C 15.716 24.317 16.096 24.951 16.707 25.278 C 16.985 25.427 17.288 25.5 17.591 25.5 C 17.955 25.5 18.317 25.394 18.631 25.185 L 30.067 17.561 C 30.59 17.212 30.902 16.629 30.902 16.001 C 30.903 15.372 30.59 14.789 30.068 14.44 Z"
            opacity=".05"
          />
          <path
            d="M 18.375 7.254 L 29.165 14.448 C 29.854 14.943 30.318 15.386 30.318 15.961 C 30.318 16.536 30.094 16.937 29.603 17.295 C 29.232 17.565 18.558 24.659 18.558 24.659 C 17.657 25.263 16.194 25.135 16.194 23.16 L 16.194 8.761 C 16.194 6.756 17.778 6.856 18.375 7.254 Z"
            opacity=".07"
          />
          <path
            fill="#fff"
            d="M 16.806 23.567 L 16.806 8.432 C 16.806 7.689 17.634 7.245 18.253 7.658 L 29.605 15.226 C 30.158 15.594 30.158 16.406 29.605 16.775 L 18.253 24.343 C 17.634 24.754 16.806 24.311 16.806 23.567 Z"
          />
        </svg>
        <svg
          v-if="embedName === 'TwitchEmbed'"
          class="w-3 md:w-6 xl:w-7 flex-shrink-0"
          viewBox="0 0 8 8"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M1.39738 0.0292969L0.953247 1.38199V6.34188H2.72979V7.24368H3.61806L4.50634 6.34188H5.83874L7.61529 4.42105V0.0292969H1.39738ZM7.17115 4.08739L5.92757 5.44008H4.35266L3.396 6.14664V5.44008H1.84152V0.480196H7.17115V4.08739Z"
            fill="white"
          />
          <path
            d="M4.50639 1.83301H4.06226V3.6366H4.50639V1.83301Z"
            fill="white"
          />
          <path
            d="M5.83879 1.83301H5.39465V3.6366H5.83879V1.83301Z"
            fill="white"
          />
        </svg>
      </div>
    </div>
    <!-- Show the embed with overlay if there's an embed -->
    <div
      v-if="showEmbed && embedData"
    >
      <div v-show="isEmbedVisible">
        <component
          ref="embed"
          :is="embedName"
          :embedData="embedData"
          :isRowFirst="isRowFirst"
          @video-buffered="videoBuffered"
          @set-is-playing="updateIsPlaying"
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
import YouTubeEmbed from "../../embeds/YouTubeFullWidth.vue";

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
    "isRowFirst",
    "isFirstVideoLoaded",
    "isMouseStopped",
    "description"
  ],
  data() {
    return {
      isEmbedVisible: false,
      isHideButtonClicked: false,
      isVideoBuffered: false,
      isVideoPlaying: false,
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
    decreaseInfoBoxSize() {
      return (
        this.isVideoBuffered && (this.isVideoPlaying || this.isEmbedVisible)
      );
    },
    isInfoBoxHidden() {
      return ( this.isMouseStopped || this.isHideButtonClicked ) && this.isVideoBuffered;
    },
  },
  methods: {
    handlePlayVideo() {
      this.isHideButtonClicked = true;

      this.playVideo();
    },
    playVideo() {
      this.$root.$emit("close-other-layouts", this.embedData.elementId);
      this.$refs.embed.startPlayer();
    },
    videoBuffered() {
      this.isVideoBuffered = true;
      if ((this.isFirstVideoLoaded || this.isRowFirst) && this.isAllowPlaying) {
        this.$emit("reset-mouse-moving");
        this.isEmbedVisible = true;
        if (!this.isFirstVideoLoaded) {
          this.activateIsMouseStopped();
        }
      } else {
        this.$emit("first-video-buffered");
        this.$refs.embed.stopPlayer();
      }
    },
    hideVideo(elementId) {
      if (!(elementId && this.embedData.elementId === elementId)) {
        this.isHideButtonClicked = false;
        this.$refs.embed.stopPlayer();
        this.isEmbedVisible = false;
      }
    },
    updateIsPlaying(newVal) {
      this.isVideoPlaying = newVal;
    },
    activateIsMouseStopped() {
      setTimeout(() => {
        this.$emit("activate-mouse-stopped");
      }, 3000);
    },
  },
  watch: {
    isAllowPlaying(newVal, oldVal) {
      if (newVal === true) {
        if (this.showOverlay || this.showArt) {
          this.isEmbedVisible = true;
        }

        this.playVideo();
      } else {
        if (this.$refs.embed.isPlaying()) {
          this.isEmbedVisible = false;
          this.isHideButtonClicked = false;
          this.$refs.embed.stopPlayer();
        }
      }
    },
  },
  mounted() {
    this.$root.$on("close-other-layouts", this.hideVideo);
  },
  destroyed() {
    this.$root.$off("close-other-layouts", this.hideVideo);
  },
};
</script>
