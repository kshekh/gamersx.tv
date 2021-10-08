<template>
  <div
    class="
      cut-edge__wrapper
      w-full
      h-full
      flex-shrink-0
      transform
      transition-transform
      hover:w-110p
      hover:h-150p
      hover:z-10
    "
    :class="{
      'cut-edge__wrapper--twitch': embedName === 'TwitchEmbed',
      'cut-edge__wrapper--youtube': embedName === 'YouTubeEmbed',
    }"
    @mouseenter="isTitleVisible = true"
    @mouseleave="isTitleVisible = false"
  >
    <div
      class="
        w-full
        h-full
        cut-edge__clipped
        cut-edge__clipped--sm-border
        cut-edge__clipped-top-left-sm
        bg-black
      "
      :class="{
        'cut-edge__clipped--twitch': embedName === 'TwitchEmbed',
        'cut-edge__clipped--youtube': embedName === 'YouTubeEmbed',
      }"
    >
      <!-- <div v-if="showArt">
        <a :href="link">
          <div v-if="image" :class="image.class" class="p-4">
            <img :width="image.width" :height="image.height" :src="image.url" />
          </div>
        </a>
      </div> -->

      <!-- Show the embed with overlay if there's an embed -->
      <div v-if="showEmbed && embedData" class="w-full h-full" @mouseenter="mouseEntered" @mouseleave="mouseLeft">
        <img v-if="showArt" v-show="isOverlayVisible" :src="image.url" class="w-full h-full">
        <img
          v-else-if="overlay"
          v-show="isOverlayVisible"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="w-full h-full"
        />
        <div class="w-full h-full flex flex-col" v-show="isEmbedVisible">
          <component
            ref="embed"
            :is="embedName"
            :embedData="embedData"
            class="flex-grow min-h-0"
            :width="'100%'"
            :height="'100%'"
          ></component>
          <a :href="link" class="flex justify-between py-1 xl:pt-2 xl:pb-3 px-2 bg-grey-900">
            <div class="mr-2">
              <h5 class="text-8 text-white font-play">{{ offlineDisplay.title }}</h5>
              <h6 class="text-7 text-grey font-play">{{ embedData.channel }}</h6>
            </div>
            <h6 class="text-7 text-grey font-play">
              {{ liveViewerCount }} viewers
            </h6>
          </a>
        </div>
      </div>

      <!-- If there's no embed, show that instead with a link first -->
      <div v-else-if="showArt & image" class="w-full h-full">
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
  </div>
</template>

<script>
import TwitchEmbed from "../../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

export default {
  name: "EmbedContainer",
  components: {
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed,
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
    },
  },
  computed: {
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
