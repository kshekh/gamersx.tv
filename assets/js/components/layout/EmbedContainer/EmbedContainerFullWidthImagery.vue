<template>
  <div class="w-full h-full flex flex-col">
    <div
      v-show="!isEmbedVisible"
      class="cut-edge__wrapper flex-grow min-h-0 w-36 md:w-86 xl:w-118 relative ease-linear"
      :class="[getGlow, {
        'opacity-0 pointer-events-none z-negative': isEmbedVisible,
        'pointer-events-none z-negative': isEmbedVisible,
      }]"
    >
      <div
        class="
          cut-edge__clipped
          cut-edge__clipped--sm-border
          cut-edge__clipped-top-right-md
          h-full
          bg-black
          overflow-hidden
        "
        :class="getOutline"
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
        <play-button
          v-if="showEmbed && embedData"
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
          "
          svgClass="w-3 md:w-7 xl:w-12"
          wrapperClass="md:pl-1.5 xl:pl-3"
          :videoType="playBtnColor"
          @click.native="playVideo"
        />
      </div>
    </div>

    <!-- Show the embed with overlay if there's an embed -->
    <div v-if="showEmbed && embedData">
      <div
        class="
          cut-edge__wrapper
          flex-grow
          min-h-0
          absolute
          inset-0
          z-20
          py-5
          md:py-8
          xl:py-12
          px-4
          md:px-18
          xl:px-32
          opacity-0
          transition-opacity
          duration-300
          ease-linear
        "
        :class="[getOutlineBorder, {
          'opacity-100': isEmbedVisible,
          'pointer-events-none z-negative': !isEmbedVisible,
        }]"
      >
        <div ref="embedWrapper" class="h-full w-full">
          <component
            ref="embed"
            :is="embedName"
            :embedData="embedData"
            class="h-full w-full border overflow-hidden bg-black"
            :class="{
              'border-purple': embedName === 'TwitchEmbed',
              'border-red': embedName === 'YouTubeEmbed',
            }"
            :width="'100%'"
            :height="'100%'"
          ></component>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TwitchEmbed from "../../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

import PlayButton from "../../helpers/PlayButton.vue";

import isBoxInViewport from "../../../mixins/isBoxInViewport";

export default {
  name: "EmbedContainerFullWidthImagery",
  mixins: [isBoxInViewport],
  components: {
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed,
    "play-button": PlayButton,
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
    "isGlowStyling"
  ],
  data() {
    return {
      isOverlayVisible: true,
      isEmbedVisible: false,
      isTitleVisible: false,
      glowStyling: {
        glow: '',
        outline: '',
        outlineBorder: ''
      }
    };
  },
  methods: {
    playVideo() {
      this.$root.$emit('close-other-layouts');
      setTimeout(() => {
        if (this.showOverlay || this.showArt) {
          this.isOverlayVisible = false;
          this.isEmbedVisible = true;
        }
      }, 0);

      window.addEventListener('scroll', this.checkIfBoxInViewPort);
      this.$refs.embed.startPlayer();
      this.$emit('hide-controls');
    },
    scrollOut() {
      if (this.showOverlay || this.showArt) {
        this.isOverlayVisible = true;
        this.isEmbedVisible = false;
      }
      // if (this.$refs.embed.isPlaying()) {
        this.$refs.embed.stopPlayer();
      // }
      window.removeEventListener('scroll', this.checkIfBoxInViewPort);
    },
    computeGlowStyling: function () {
      if (this.isGlowStyling === "Enabled" || this.isGlowStyling === "Enabled if Live") {
        if (this.embedName === 'TwitchEmbed') {
          this.glowStyling.outline = 'cut-edge__clipped--twitch';
          this.glowStyling.outlineBorder = 'cut-edge__clipped--twitch border-purple';
          this.glowStyling.glow = 'cut-edge__wrapper--twitch';
        }
        else if (this.embedName === 'YouTubeEmbed') {
          this.glowStyling.outline = 'cut-edge__clipped--youtube';
          this.glowStyling.outlineBorder = 'cut-edge__clipped--youtube border-red';
          this.glowStyling.glow = 'cut-edge__wrapper--youtube';
        }
      }
    }
  },
  computed: {
    playBtnColor() {
      return this.embedName === "TwitchEmbed" ? "twitch" : "youtube";
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
    getOutline: function () {
      this.computeGlowStyling();
      return this.glowStyling.outline;
    },
    getGlow: function () {
      this.computeGlowStyling();
      return this.glowStyling.glow;
    },
    getOutlineBorder: function () {
      this.computeGlowStyling();
      return this.glowStyling.outlineBorder;
    }
  },
  mounted() {
    this.$root.$on("close-other-layouts", this.scrollOut);
    this.isOverlayVisible = this.showOverlay;
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  },
  destroyed() {
    this.$root.$off("close-other-layouts", this.scrollOut);
  }
};
</script>
