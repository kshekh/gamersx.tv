<template>
  <div
    class="
      cut-edge__wrapper
      w-full
      h-full
      flex-shrink-0
      transform
      transition-all
      duration-300
      hover:h-110p
      md:hover:h-115p
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
      <!-- Show the embed with overlay if there's an embed -->
      <div
        v-if="showEmbed && embedData"
        class="w-full h-full relative flex flex-col"
        @mouseenter="mouseEntered"
        @mouseleave="mouseLeft"
      >
        <div v-show="isOverlayVisible" class="w-full h-full overflow-hidden flex-grow relative">
          <img v-if="showArt" :src="image.url" class="relative top-1/2 transform -translate-y-1/2 w-full" />
          <img
            v-else-if="overlay"
            alt="Embed's Custom Overlay"
            :src="overlay"
            class="relative top-1/2 transform -translate-y-1/2 w-full"
          />
          <div
            class="
              absolute
              top-3
              left-6
              md:left-2
              xl:left-6
              py-px
              px-1.5
              bg-purple
              text-white text-xxs text-center
              font-play
              min-w-40
            "
          >
            <span>3:05:09</span>
          </div>
          <div
            class="
              absolute
              bottom-1.5
              left-6
              md:left-2
              xl:left-6
              py-px
              px-1.5
              bg-purple
              text-white text-xxs text-center
              font-play
              min-w-40
            "
          >
            <span>{{ liveViewerCount }} views</span>
          </div>
          <div
            class="
              absolute
              bottom-1.5
              right-6
              md:right-2
              xl:right-6
              py-px
              px-1.5
              bg-red
              text-white text-xxs text-center
              font-play
              min-w-40
            "
          >
            <span>Online</span>
          </div>
          <play-button
            class="
              absolute
              top-1/2
              left-1/2
              transform
              -translate-x-1/2 -translate-y-1/2
              z-20
            "
            :videoType="playBtnColor"
          />
        </div>
        <div
          v-show="isOverlayVisible"
          class="py-1.5 md:py-0.5 xl:py-1.5 px-3 md:px-2 xl:px-3 flex items-center"
          :class="{
            'bg-purple': embedName === 'TwitchEmbed',
            'bg-red': embedName === 'YouTubeEmbed',
          }"
        >
          <img src="https://picsum.photos/id/1062/100/100" alt="avatar" class="w-9 h-9 md:w-5 md:h-5 xl:w-9 xl:h-9 rounded-full mr-1.5">
          <div class="overflow-hidden">
            <h5 class="text-xxs text-white font-play truncate">
              {{ offlineDisplay.title }}
            </h5>
            <h6 class="text-8 text-grey-600 font-play truncate">
              {{ embedData.channel }}
            </h6>
          </div>
        </div>

        <div
          class="w-full h-full flex flex-col relative"
          v-show="isEmbedVisible"
        >
          <div class="absolute left-4 md:left-3 xl:left-6 top-2 w-2/3">
            <h5 class="text-xxs text-white font-play truncate">
              {{ offlineDisplay.title }}
            </h5>
            <h6 class="text-8 text-white font-play truncate">
              {{ embedData.channel }}
            </h6>
          </div>
          <component
            ref="embed"
            :is="embedName"
            :embedData="embedData"
            class="flex-grow min-h-0"
            :width="'100%'"
            :height="'100%'"
          ></component>
          <a
            :href="link"
            class="
              flex
              justify-between
              py-1
              xl:pt-3
              xl:pb-3
              px-3
              md:px-2
              xl:px-4
              bg-grey-900
            "
          >
            <div class="mr-2">
              <h5 class="text-xxs text-white font-play">
                {{ offlineDisplay.title }}
              </h5>
              <h6 class="text-8 text-grey font-play">
                {{ embedData.channel }}
              </h6>
            </div>
            <h6 class="text-8 text-grey font-play">
              {{ liveViewerCount }} viewers
            </h6>
          </a>
        </div>
      </div>

      <!-- If there's no embed, show that instead with a link first -->
      <div v-else-if="showArt && image" class="w-full h-full">
        <a :href="link" class="block w-full h-full overflow-hidden">
          <img :src="image.url" class="relative top-1/2 transform -translate-y-1/2 w-full" />
        </a>
      </div>

      <!-- If there's only an overlay and isn't art, show that instead with a link -->
      <div v-else-if="showOverlay" class="w-full h-full">
        <a :href="link" class="block w-full h-full overflow-hidden">
          <img
            class="relative top-1/2 transform -translate-y-1/2 w-full"
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

import PlayButton from "../../helpers/PlayButton.vue";

export default {
  name: "EmbedContainerClassicMd",
  components: {
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed,
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
      setTimeout(() => {
        if (this.showOverlay || this.showArt) {
          this.isOverlayVisible = false;
          this.isEmbedVisible = true;
        }
      }, 0)
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
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  },
};
</script>
