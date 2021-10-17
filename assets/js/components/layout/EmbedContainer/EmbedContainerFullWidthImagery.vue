<template>
  <!-- <div @mouseenter="mouseEntered" @mouseleave="mouseLeft" class="w-full h-full "> -->
  <div class="w-full h-full flex flex-col">
    <div
      class="cut-edge__wrapper flex-grow min-h-0 w-36 md:w-86 xl:w-118 relative"
      :class="{
        'cut-edge__wrapper--twitch': embedName === 'TwitchEmbed',
        'cut-edge__wrapper--youtube': embedName === 'YouTubeEmbed',
      }"
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
        :class="{
          'cut-edge__clipped--twitch': embedName === 'TwitchEmbed',
          'cut-edge__clipped--youtube': embedName === 'YouTubeEmbed',
        }"
      >
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
        <play-button
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
          :videoType="playBtnColor"
        />
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
import TwitchEmbed from "../../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

import PlayButton from "../../helpers/PlayButton.vue";

export default {
  name: "EmbedContainerFullWidthImagery",
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
  ],
  data() {
    return {
      isOverlayVisible: true,
      isEmbedVisible: false,
      isTitleVisible: false,
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
    },
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
  },
  mounted() {
    this.isOverlayVisible = this.showOverlay;
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  },
};
</script>
