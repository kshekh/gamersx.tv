<template>
  <div class="cursor-default w-full h-full shrink-0 " ref="itemWrapper" v-if="!isMobileDevice">
    <div class="cut-edge__wrapper w-full h-full" :class="getGlow">
      <div
        @click="isShowTwitchEmbed = true"
        class="w-full h-full border-[3px] rounded-[10px] overflow-hidden !border-[#7A4ECC]/40  cut-edge__clipped-top-left-sm bg-black"
        :class="getOutline"
      >
        <!-- Show the embed with overlay if there's an embed -->
        <div
          v-if="showEmbed && embedData"
          class="w-full h-full relative overflow-hidden"
          @click="handleClick(embedData)"
        >
          <h1>click</h1>
          <img
            v-if="showArt && image"
            :src="image.url"
            class="relative top-1/2 transform -translate-y-1/2 w-full object-fit"
          />
          <img
            v-else-if="showOverlay"
            alt="Embed's Custom Overlay"
            :src="overlay"
            class="relative top-1/2 transform -translate-y-1/2 w-full object-cover"
          />
          <!--          <img-->
          <!--            v-if="showEmbed && embedData"-->
          <!--            src="/images/live-icon.gif"-->
          <!--            class="" style="position: Layout-sc-1xcs6mc-0 top-bar--pointer-enabledLayout-sc-1xcs6mc-0 top-bar--pointer-enabled;top: 10px;width: 75px;right: 0;"-->
          <!--          />-->
          <play-button
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 pointer-events-none"
            :videoType="playBtnColor"
          />
        </div>

        <!-- If there's no embed, show that instead with a link first -->
        <div v-else-if="showArt && image" class="w-full h-full">
          <a :href="link" class="block w-full h-full overflow-hidden">
            <img
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
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
            />
          </a>
        </div>
      </div>
    </div>

    <div
      v-if="showEmbed && embedData"
      ref="embedWrapper"
      :style="embedSize"
      :class="[
        'cut-edge__wrapper',
        'absolute',
        'z-30',
        'transition-opacity-transform',
        'ease-linear',
        'duration-500',
        getGlow,
        {
          invisible: !isEmbedVisible,
        },
      ]"
    >
      <CommonContainer
        @on-pin="onPinHandler"
        @close-container="() => closeContainer(true)"
        @on-mouse-down="onMouseDownHandler"
        :isPinActive="isPinBtnActive"
        :isMoveActive="isMoveBtnActive"
        :innerWrapperClassNames="getOutline"
      >
        <div class="flex-grow min-h-0 relative">
          <div class="absolute inset-0 bg-black overflow-hidden">
            <img
              v-if="showArt && image"
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="overlay"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
          </div>
          <div
            class="relative w-full h-full transition-opacity ease-linear duration-500 delay-750 opacity-0 bg-black"
            :class="{ 'opacity-100': isEmbedVisible }"
          >
            <div class="absolute left-4 md:left-3 xl:left-6 top-2 w-2/3">
              <h5 class=" cursor-default text-xxs text-white font-play truncate">
                {{ offlineDisplay.title }}
              </h5>
              <h6 class="cursor-default text-8 text-white font-play truncate">
                {{ embedData.channel }}
              </h6>
            </div>
            <component
              v-if="embedData"
              ref="embed"
              :isShowTwitchEmbed="isShowTwitchEmbed"
              :is="embedName"
              :embedData="embedData"
              :overlay="overlay"
              :image="image"
              class="w-full h-full"
              :width="'100%'"
              :height="'100%'"
            ></component>
          </div>
        </div>
<!--        <a-->
<!--          :href="link"-->
<!--          class="cursor-default flex justify-between py-1 xl:pt-3 xl:pb-3 px-3 md:px-2 xl:px-4 bg-grey-900"-->
<!--          :title="offlineDisplay.title"-->
<!--        >-->
<!--          <div class="mr-2 overflow-hidden">-->
<!--            <h5-->
<!--              class="cursor-default text-xxs text-white font-play overflow-hidden text-ellipsis whitespace-nowrap"-->
<!--            >-->
<!--              {{ offlineDisplay.title }}-->
<!--            </h5>-->
<!--            <h6-->
<!--              class="cursor-default text-8 text-grey font-play overflow-hidden text-ellipsis whitespace-nowrap"-->
<!--            >-->
<!--              {{ embedData.channel }}-->
<!--            </h6>-->
<!--          </div>-->
<!--          <h6 class="cursor-default text-8 text-grey font-play whitespace-nowrap">-->
<!--            {{ liveViewerCount }} viewers-->
<!--          </h6>-->
<!--        </a>-->
      </CommonContainer>
    </div>
  </div>

  <div class="w-full h-full shrink-0" v-else>
    <div
      @click="isShowTwitchEmbed = true"
      v-show="!isEmbedVisible"
      class="cut-edge__wrapper h-full w-full md:w-50 xs:pr-26 sm:pr-16 lg:pr-0 xl:pr-16 2xl:pr-0 xs:w-75 md:w-75 lg:w-80 xl:w-118 2xl:w-127"
      style="aspect-ratio: 3/4"
      :class="getGlow"
    >
      <div
        class="cut-edge__clipped cut-edge__clipped-top-right-md h-full bg-black overflow-hidden"
        :class="getOutline"
      >
        <img
          v-if="showArt && image"
          :src="image.url"
          class="-translate-y-1/2 relative top-1/2 transform h-full object-cover"
        />

        <img
          v-else-if="showOverlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="relative top-1/2 transform -translate-y-1/2 w-full h-full object-cover"
        />
        <play-button
          v-if="showEmbed && embedData"
          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 h-12 md:h-16 xl:h-32 w-12 md:w-16 xl:w-32"
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
        class="cut-edge__wrapper flex-grow min-h-0 absolute inset-0 z-20 py-5 md:py-8 xl:py-12 px-4 md:px-18 xl:px-32 opacity-0 transition-opacity duration-300 ease-linear custom-embed-m"
        :class="[
          getOutlineBorder,
          {
            'opacity-100': isEmbedVisible,
            'pointer-events-none z-negative': !isEmbedVisible,
          },
        ]"
        style="
          top: 50%;
          left: 0;
          transform: translateY(-50%);
          z-index: 99;
          display: flex;
          align-items: center;
          width: auto !important;
        "
      >
        <div
          ref="embedWrapper"
          :class="{ 'relative w-full h-full main-parent': true }"
        >
          <component
            v-if="embedData"
            ref="embed"
            :is="embedName"
            :embedData="embedData"
            :overlay="overlay"
            :image="image"
            :isShowTwitchEmbed="isShowTwitchEmbed"
            :isMobileDevice="isMobileDevice"
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

import embedMixin from "../../../mixins/embedFrameMixin";
import CommonContainer from "../CommonContainer/CommonContainer.vue";
import PlayButton from "../../helpers/PlayButton.vue";

export default {
  name: "EmbedContainerClassicLg",
  mixins: [embedMixin],
  components: {
    CommonContainer: CommonContainer,
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
      isMobileDevice: false,
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
  mounted() {
    this.setIsMobileDevice();
  },
  methods: {
    handleClick: function (embedData) {
      this.resetContainerStyles();
      this.clickContainer(embedData.elementId, false);
      this.setContainerStyles();
    },
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
    playVideo() {
      this.$root.$emit("close-other-layouts");
      setTimeout(() => {
        if (this.showOverlay || this.showArt) {
          this.isOverlayVisible = false;
          this.isEmbedVisible = true;
        }
      }, 0);
      window.addEventListener("scroll", this.checkIfBoxInViewPort);
      this.$refs.embed.startPlayer();

      this.$emit("hide-controls");
    },
    scrollOut() {
      if (this.$root.isVisibleVideoContainer) {
        return;
      }
      console.log('It was me!');
      if (this.showOverlay || this.showArt) {
        this.isOverlayVisible = true;
        this.isEmbedVisible = false;
      }
      // if (this.$refs.embed.isPlaying()) {
      this.$refs.embed.stopPlayer();
      // }
      window.removeEventListener("scroll", this.checkIfBoxInViewPort);
      this.$emit("show-controls");
    },
    setIsMobileDevice() {
      const checkDeviceType = navigator.userAgent
        .toLowerCase()
        .match(/mobile/i);
      if (checkDeviceType) {
        this.isMobileDevice = true;
      } else {
        this.isMobileDevice = false;
      }
    },
  },
};
</script>
