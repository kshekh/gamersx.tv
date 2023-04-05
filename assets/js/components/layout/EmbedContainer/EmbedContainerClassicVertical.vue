<template>
  <div class="w-full h-full shrink-0" ref="itemWrapper">
    <div
      @click="isShowTwitchEmbed = true"
      class="cut-edge__wrapper w-full h-full"
      :class="getGlow"
      style="aspect-ratio: 16/9"
    >
      <div
        class="w-full h-full cut-edge__clipped--sm-border cut-edge__clipped-top-left-sm bg-black"
        :class="getOutline"
      >
        <!-- Show the embed with overlay if there's an embed -->
        <div
          v-if="showEmbed && embedData"
          class="w-full h-full relative overflow-hidden"
          @mouseenter="mouseEntered"
          @mouseleave="mouseLeave"
        >
          <img
            v-if="showArt && image"
            :src="image.url"
            class="relative top-1/2 transform -translate-y-1/2 w-full"
            style="height: inherit"
          />
          <img
            v-else-if="showOverlay"
            alt="Embed's Custom Overlay"
            :src="overlay"
            class="relative top-1/2 transform -translate-y-1/2 w-full"
            style="height: inherit"
          />
          <!--          <img-->
          <!--            v-if="showEmbed && embedData"-->
          <!--            src="/images/live-icon.gif"-->
          <!--            class="" style="position: absolute;top: 0px;width: 75px;right: 0;"-->
          <!--          />-->
        </div>

        <!-- If there's no embed, show that instead with a link first -->
        <div v-else-if="showArt && image" class="w-full h-full">
          <a :href="link" class="block w-full h-full overflow-hidden">
            <img
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
              style="height: inherit"
            />
          </a>
        </div>

        <!-- If there's only an overlay and isn't art, show that instead with a link -->
        <div v-else-if="showOverlay" class="w-full h-full">
          <a :href="link" class="block w-full h-full overflow-hidden">
            <img
              class="relative top-1/2 transform -translate-y-1/2 w-full"
              alt="Embed's Custom Overlay"
              :src="overlay"
              style="height: inherit"
            />
          </a>
        </div>
      </div>
    </div>
    <div
      v-if="showEmbed && embedData"
      class="cut-edge__wrapper absolute z-30 transition-opacity-transform ease-linear duration-500"
      :class="[
        getGlow,
        {
          invisible: !isEmbedVisible,
        },
      ]"
      ref="embedWrapper"
      :style="embedSize"
    >
      <div
        class="w-full h-full flex flex-col relative cut-edge__clipped cut-edge__clipped--sm-border cut-edge__clipped-top-left-sm bg-black"
        :class="getOutline"
      >
        <div class="flex-grow min-h-0 relative">
          <div class="absolute inset-0 bg-black overflow-hidden">
            <img
              v-if="showArt && image"
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
              style="height: inherit"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="overlay"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
              style="height: inherit"
            />
          </div>
          <div
            class="relative w-full h-full transition-opacity ease-linear duration-500 delay-750 opacity-0 bg-black"
            :class="{ 'opacity-100': isEmbedVisible }"
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
              v-if="embedData"
              ref="embed"
              :is="embedName"
              :embedData="embedData"
              :overlay="overlay"
              :image="image"
              :isShowTwitchEmbed="isShowTwitchEmbed"
              class="w-full h-full"
              :width="'100%'"
              :height="'100%'"
            ></component>
          </div>
        </div>
        <a
          :href="link"
          class="flex justify-between py-1 xl:pt-3 xl:pb-3 px-3 md:px-2 xl:px-4 bg-grey-900"
          :title="offlineDisplay.title"
        >
          <div class="mr-2 overflow-hidden">
            <h5
              class="text-xxs text-white font-play overflow-hidden text-ellipsis whitespace-nowrap"
            >
              {{ offlineDisplay.title }}
            </h5>
            <h6
              class="text-8 text-grey font-play overflow-hidden text-ellipsis whitespace-nowrap"
            >
              {{ embedData.channel }}
            </h6>
          </div>
          <h6 class="text-8 text-grey font-play whitespace-nowrap">
            {{ liveViewerCount }} viewers
          </h6>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import TwitchEmbed from "../../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

import embedMixin from "../../../mixins/embedFrameMixin";

export default {
  name: "EmbedContainerClassicVertical",
  mixins: [embedMixin],
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
    "isGlowStyling",
    "isCornerCut",
  ],
  data: function () {
    return {
      glowStyling: {
        glow: "",
      },
      cornerCutStyling: {
        outline: "",
      },
      isShowTwitchEmbed: false,
    };
  },
  computed: {
    getOutline: function () {
      this.computeGlowStyling();
      return this.cornerCutStyling.outline;
    },
    getGlow: function () {
      this.computeGlowStyling();
      return this.glowStyling.glow;
    },
  },
  methods: {
    computeGlowStyling: function () {
      if (
        this.isGlowStyling === "always_on" ||
        (this.isGlowStyling === "enabled_if_live" && this.showOnline) ||
        (this.isGlowStyling === "enabled_if_offline" && !this.showOnline)
      ) {
        if (this.embedName === "TwitchEmbed") {
          this.glowStyling.glow = "cut-edge__wrapper--twitch";
        } else if (this.embedName === "YouTubeEmbed") {
          this.glowStyling.glow = "cut-edge__wrapper--youtube";
        }
      }

      if (
        this.isCornerCut === "always_on" ||
        (this.isCornerCut === "enabled_if_live" && this.showOnline) ||
        (this.isCornerCut === "enabled_if_offline" && !this.showOnline)
      ) {
        if (this.embedName === "TwitchEmbed") {
          this.cornerCutStyling.outline = "cut-edge__clipped--twitch";
        } else if (this.embedName === "YouTubeEmbed") {
          this.cornerCutStyling.outline = "cut-edge__clipped--youtube";
        }
      }
    },
  },
  // created() {
  //   if(!this.showOnline && this.embedData){
  //     this.mouseEntered();
  //   }
  // }
};
</script>
