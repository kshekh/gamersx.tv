<template>
  <div class="w-full h-full shrink-0" ref="itemWrapper" v-if="!isMobileDevice">
    <div class="cut-edge__wrapper w-full h-full" :class="getGlow">
      <div
        @click="isShowTwitchEmbed = true"
        class="w-full h-full cut-edge__clipped cut-edge__clipped--sm-border cut-edge__clipped-top-left-sm bg-black"
        :class="getOutline"
      >
        <!-- Show the embed with overlay if there's an embed -->
        <div
          v-if="showEmbed && embedData"
          class="w-full h-full relative flex flex-col"
          @click="clickContainer(embedData.elementId)"
        >
          <div class="w-full h-full overflow-hidden flex-grow relative">
            <img
              v-if="showArt && image"
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full object-fit"
              style="height: inherit"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="overlay"
              onerror="this.onerror=null; this.src='https://placehold.co/600x400'"
              class="relative top-1/2 transform -translate-y-1/2 w-full object-cover"
              style="height: inherit"
            />
            <!--            <img-->
            <!--              v-if="showEmbed && embedData"-->
            <!--              src="/images/live-icon.gif"-->
            <!--              class="" style="position: absolute;top: 0px;width: 75px;right: 0;"-->
            <!--            />-->
            <div
              class="invisible absolute top-3 left-6 md:left-2 xl:left-6 py-px px-1.5 bg-purple text-white text-xxs text-center font-play min-w-40"
            >
              <span>3:05:09</span>
            </div>
            <div
              class="absolute bottom-1.5 left-6 md:left-2 xl:left-6 py-px px-1.5 bg-purple text-white text-xxs text-center font-play min-w-40"
            >
              <span>{{ liveViewerCount }} views</span>
            </div>
            <!--<div
              class="absolute bottom-1.5 right-6 md:right-2 xl:right-6 py-px px-1.5 bg-red text-white text-xxs text-center font-play min-w-40"
            >
              <span>Online</span>
            </div>-->
            <play-button
              class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 pointer-events-none"
              :videoType="playBtnColor"
            />
          </div>
          <div
            class="py-1.5 md:py-0.5 xl:py-1.5 px-3 md:px-2 xl:px-3 flex items-center"
            :class="{
              'bg-purple': embedName === 'TwitchEmbed',
              'bg-red': embedName === 'YouTubeEmbed',
            }"
          >
            <img
              :src="
                streamersData
                  ? streamersData
                  : 'https://picsum.photos/id/1062/100/100'
              "
              alt="avatar"
              class="w-9 h-9 md:w-5 md:h-5 xl:w-9 xl:h-9 rounded-full mr-1.5"
              style="height: inherit"
            />
            <div class="overflow-hidden">
              <h5 class="text-xxs text-white font-play truncate">
                {{ offlineDisplay.title }}
              </h5>
              <h6 class="text-8 text-grey-600 font-play truncate">
                {{ embedData.channel }}
              </h6>
            </div>
          </div>
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
              onerror="this.onerror=null; this.src='https://placehold.co/600x400'"
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
      <CommonContainer
        @on-pin="onPinHandler"
        @close-container="closeContainer"
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
              style="height: inherit"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="overlay"
              onerror="this.onerror=null; this.src='https://placehold.co/600x400'"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
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
      </CommonContainer>
    </div>
  </div>
  <!--Additional block added for mobile to fix issue embed not play in iOS-->
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
          class="-translate-y-1/2 relative top-1/2 transform w-30p h-full object-cover"
        />

        <img
          v-else-if="showOverlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          onerror="this.onerror=null; this.src='https://placehold.co/600x400'"
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
import CommonContainer from "../CommonContainer/CommonContainer.vue";
import embedMixin from "../../../mixins/embedFrameMixin";
import PlayButton from "../../helpers/PlayButton.vue";

export default {
  name: "EmbedContainerClassicMd",
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
    "info",
    "profileImageUrl",
  ],
  data: function () {
    return {
      glowStyling: {
        glow: "",
      },
      cornerCutStyling: {
        outline: "",
      },
      streamersData: {},
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
    streamerInfoApi: function () {
      if (this.embedName === "TwitchEmbed") {
        this.streamersData = this.profileImageUrl;
      } else if (this.embedName === "YouTubeEmbed") {
        // this.streamersData = this.info.snippet.thumbnails.default.url;   //This is video thumbnail but we need channel profile which can get from following key
        this.streamersData = this.profileImageUrl;
      }
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
  beforeMount() {
    this.streamerInfoApi();
  },
};
</script>
