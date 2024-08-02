<template>
  <div
    ref="embedWrapper"
    @swiped-left="forward()"
    @swiped-right="back()"
    @mouseenter="mouseEntered()"
    @mousemove="checkMouseActive()"
    class="home-row mb-7 md:mb-9 xl:mb-14 bg-cover bg-no-repeat relative min-h-mobile home-banner-section"
    :class="{ 'mobile-full-width': isMobileDevice }"
    :style="customBg"
  >
    <div class="container mx-auto">
      <div class="pb-50p"></div>
      <div class="px-4 md:px-12 flex items-center justify-between absolute inset-0 z-10">
        <SliderArrow
          :isNext="false"
          :videoType="currentChannelEmbedName"
          @arrow-clicked="back()"
        />

        <div
          class="py-2 md:pt-8 xl:py-14 flex-grow min-w-0 flex h-full items-center justify-between md:flex-col"
        >
          <div
            ref="channelBox"
            class="mr-5 md:mr-0 md:pt-4 w-full flex-grow flex flex-col justify-center min-h-mobile-description"
          >
            <div
              ref="channelDivs"
              v-for="(channel, index) in displayChannels"
              :key="index"
            >
              <!--channel.componentName possible values: EmbedContainer-->
              <component
                :is="channel.componentName"
                v-if="index === rowIndex"
                v-bind="channel"
                :isAllowPlaying="isAllowPlaying"
                :isRowFirst="isRowFirst"
                :isFirstVideoLoaded="isFirstVideoLoaded"
                :isMouseStopped="isMouseStopped"
                :customBg="customBg"
                @first-video-buffered="handleFirstVideoLoaded"
                @activate-mouse-stopped="activateMouseStopped"
                @reset-mouse-moving="checkMouseActive"
              ></component>
            </div>
          </div>
          <div
            class="flex items-center space-x-1 md:space-x-2 z-10 self-end md:self-center absolute -bottom-[30px]"
          >
            <div
              ref="sliderDotRef"
              class="flex items-center space-x-1 md:space-x-2 z-10 self-end md:self-center absolute -bottom-[30px]"
            >
              <SliderDot
                v-for="(channel, index) in displayChannels"
                :key="'channelDot' + index"
                :embedType="currentChannelEmbedName"
                :dotIndex="index"
                :isDotActive="index === rowIndex"
                @slider-dot-clicked="setActiveChannel"
              />
            </div>
          </div>
        </div>

        <SliderArrow
          :isNext="true"
          :videoType="currentChannelEmbedName"
          @arrow-clicked="forward()"
        />
      </div>
    </div>

    <div
      v-if="showEmbed && currentChannel && currentChannel.embedData"
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
        @on-pin="(ev) => onPinHandler(ev, true)"
        @close-container="() => closeContainer(true)"
        @on-mouse-down="(ev) => onMouseDownHandler(ev,true)"
        :isPinActive="isPinBtnActive"
        :isMoveActive="isMoveBtnActive"
        :innerWrapperClassNames="getOutline"
      >
        <div class="flex-grow min-h-0 relative">
          <div class="absolute inset-0 bg-black overflow-hidden">
            <img
              v-if="showArt && image"
              :src="currentChannel.image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="currentChannel.overlay"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
          </div>
          <div
            class="relative w-full h-full transition-opacity ease-linear duration-500 delay-750 opacity-0 bg-black"
            :class="{ 'opacity-100': isEmbedVisible }"
          >
            <div class="absolute left-4 md:left-3 xl:left-6 top-2 w-2/3">
              <h5 class="cursor-default text-xxs text-white font-play truncate">
                {{ currentChannel.offlineDisplay.title }}
              </h5>
              <h6 class="cursor-default text-8 text-white font-play truncate">
                {{ currentChannel.embedData.channel }}
              </h6>
            </div>
            <component
              v-if="currentChannel.embedData"
              ref="embed"
              :is="currentChannel.embedName"
              :embedData="currentChannel.embedData"
              :overlay="currentChannel.overlay"
              :image="currentChannel.image"
              :isShowTwitchEmbed="isShowTwitchEmbed"
              class="w-full h-full"
              :width="'100%'"
              :height="'100%'"
            ></component>
          </div>
        </div>
        <a
          :href="currentChannel.link"
          class="cursor-default flex justify-between py-1 xl:pt-3 xl:pb-3 px-3 md:px-2 xl:px-4 bg-grey-900"
          :title="currentChannel.offlineDisplay.title"
        >
          <div class="cursor-default mr-2 overflow-hidden">
            <h5
              class="cursor-default text-xxs text-white font-play overflow-hidden text-ellipsis whitespace-nowrap"
            >
              {{ currentChannel.offlineDisplay.title }}
            </h5>
            <h6
              class="cursor-default text-8 text-grey font-play overflow-hidden text-ellipsis whitespace-nowrap"
            >
              {{ currentChannel.embedData.channel }}
            </h6>
          </div>
          <h6
            class="cursor-default text-8 text-grey font-play whitespace-nowrap"
          >
            {{ currentChannel.liveViewerCount }} viewers
          </h6>
        </a>
      </CommonContainer>
    </div>
  </div>
</template>

<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerFullWidthDescriptive.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerDescriptive.vue";
import embedMixin from "../../mixins/embedFrameMixin";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";
import CommonContainer from "../layout/CommonContainer/CommonContainer.vue";
import SliderDot from "../helpers/SliderDot.vue";
import SliderArrow from "../helpers/SliderArrow.vue";
import TwitchEmbed from "../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../embeds/YouTubeEmbed.vue";

import isBoxInViewport from "../../mixins/isBoxInViewport";

require("swiped-events");

export default {
  name: "FullWidthDescriptive",
  mixins: [isBoxInViewport, embedMixin],
  components: {
    CommonContainer: CommonContainer,
    EmbedContainer: EmbedContainer,
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    SliderDot: SliderDot,
    SliderArrow: SliderArrow,
  },
  props: {
    settings: {
      type: Object,
      required: true,
    },
    rowPosition: {
      type: Number,
      required: true,
    },
  },
  data: function () {
    return {
      rowIndex: 0,
      displayChannels: [],
      isAllowPlaying: true,
      isFirstVideoLoaded: false,
      isMouseStopped: false,
      isMouseMovingTimeout: 0,
      isMobileDevice: false,
      currentChannel: null,
      isEmbedVisible: false,
      isShowTwitchEmbed: false,
      glowStyling: {
        glow: "",
      },
      cornerCutStyling: {
        outline: "",
        outlineBorder: "",
      },
    };
  },
  computed: {
    showEmbed() {
      return (
        (this.currentChannel && this.currentChannel?.showOnline &&
          this.currentChannel?.onlineDisplay.showEmbed) ||
        (!this.currentChannel?.showOnline &&
          this.currentChannel?.offlineDisplay.showEmbed)
      );
    },
    showArt() {
      return (
        (this.currentChannel && this.currentChannel?.showOnline &&
          this.currentChannel?.onlineDisplay.showArt) ||
        (!this.currentChannel?.showOnline &&
          this.currentChannel?.offlineDisplay.showArt)
      );
    },
    showOverlay() {
      return (
        this.currentChannel && this.currentChannel?.overlay &&
        ((this.currentChannel?.showOnline &&
          this.currentChannel?.onlineDisplay.showOverlay) ||
          (!this.currentChannel?.showOnline &&
            this.currentChannel?.offlineDisplay.showOverlay))
      );
    },
    getOutline: function () {
      this.computeGlowStyling();
      return this.cornerCutStyling.outline;
    },
    getGlow: function () {
      this.computeGlowStyling();
      return this.glowStyling.glow;
    },
    getOutlineBorder: function () {
      this.computeGlowStyling();
      return this.cornerCutStyling.outlineBorder;
    },
    isRowFirst() {
      return this.rowPosition === 0;
    },
    customBg: function () {
      let selected = this.displayChannels[this.rowIndex];

      if (selected && selected.customArt) {
        return {
          // backgroundImage: "url(https://picsum.photos/2000/3000)"
          backgroundImage: "url(" + selected.customArt + ")",
          backgroundSize: "100% 100%",
        };
      } else {
        return {};
      }
    },
    currentChannelEmbedName() {
      let selected = this.displayChannels[this.rowIndex];

      if (selected && selected.embedName) {
        return selected.embedName;
      } else {
        // Default for now
        return "TwitchEmbed";
      }
    },
    currentChannelEmbed() {
      let selected = this.displayChannels && this.displayChannels[this.rowIndex];

      if (selected) {
        return selected;
      } else {
        // Default for now
        return "TwitchEmbed";
      }
    },
  },
  methods: {
    showChannel: function (channel) {
      return (
        (channel.showOnline &&
          (channel.onlineDisplay.showArt ||
            channel.onlineDisplay.showEmbed ||
            channel.onlineDisplay.showOverlay)) ||
        (!channel.showOnline &&
          (channel.offlineDisplay.showArt ||
            channel.offlineDisplay.showEmbed ||
            channel.offlineDisplay.showOverlay))
      );
    },
    first: function () {
      this.rowIndex = 0;
      this.reorder();
    },
    back: function () {
      this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
      this.reorder();
    },
    forward: function () {
      this.rowIndex = (this.rowIndex + 1).mod(this.displayChannels.length);
      this.reorder();
    },
    reorder() {
      this.checkMouseActive();
      for (let i = 0; i < this.$refs.channelDivs.length; i++) {
        let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
        // Add one to j because flexbox order should start with 1, not 0
        this.$refs.channelDivs[i].style.order = j + 1;
      }
    },
    setActiveChannel(channelIndex) {
      this.rowIndex = channelIndex;
    },
    mouseEntered() {
      this.isAllowPlaying = true;
      window.addEventListener("scroll", this.checkIfBoxInViewPort);
    },
    scrollOut() {
      if (this.$root.isVisibleVideoContainer) {
        return;
      }
      console.log("I am allowed to play: ", this.isAllowPlaying);
      this.isAllowPlaying = false;
      this.isMouseStopped = false;
      clearTimeout(this.isMouseMovingTimeout);
      window.removeEventListener("scroll", this.checkIfBoxInViewPort);
    },
    handleFirstVideoLoaded() {
      this.isFirstVideoLoaded = true;
    },
    activateMouseStopped() {
      this.isMouseStopped = true;
    },
    checkMouseActive() {
      this.isMouseStopped = false;
      clearTimeout(this.isMouseMovingTimeout);
      this.isMouseMovingTimeout = setTimeout(() => {
        this.isMouseStopped = true;
      }, 3000);
    },
    setIsMobileDevice: function () {
      const checkDeviceType = navigator.userAgent
        .toLowerCase()
        .match(/mobile/i);
      if (checkDeviceType) {
        this.isMobileDevice = true;
      } else {
        this.isMobileDevice = false;
      }
    },
    computeGlowStyling: function () {
      if (
        this.currentChannel && this.currentChannel.isGlowStyling === "always_on" ||
        (this.currentChannel.isGlowStyling === "enabled_if_live" &&
          this.showOnline) ||
        (this.currentChannel.isGlowStyling === "enabled_if_offline" &&
          !this.showOnline)
      ) {
        if (this.currentChannel && this.currentChannel.embedName === "TwitchEmbed") {
          this.glowStyling.glow = "cut-edge__wrapper--twitch";
          this.cornerCutStyling.outlineBorder =
            "cut-edge__clipped--twitch border-purple";
        } else if (this.currentChannel && this.currentChannel.embedName === "YouTubeEmbed") {
          this.glowStyling.glow = "cut-edge__wrapper--youtube";
          this.cornerCutStyling.outlineBorder =
            "cut-edge__clipped--youtube border-red";
        }
      }

      if (
        this.isCornerCut === "always_on" ||
        (this.isCornerCut === "enabled_if_live" && this.showOnline) ||
        (this.isCornerCut === "enabled_if_offline" && !this.showOnline)
      ) {
        if (this.currentChannel && this.currentChannel.embedName === "TwitchEmbed") {
          this.cornerCutStyling.outline = "cut-edge__clipped--twitch";
        } else if (this.currentChannel && this.currentChannel.embedName === "YouTubeEmbed") {
          this.cornerCutStyling.outline = "cut-edge__clipped--youtube";
        }
      }
    },
  },
  mounted() {
    const refItem = this.$refs.sliderDotRef.getBoundingClientRect().top;

    if (!this.isRowFirst) {
      this.isAllowPlaying = false;
    }
    // else {
    //   window.addEventListener("scroll", this.checkScrollPosition);
    // }

    const options = {
      root: null, // Use the viewport as the root
      rootMargin: '0px', // No margin needed for this specific use case
      threshold: 0.1 // Trigger when 10% of the embed wrapper is visible
    };

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.intersectionRatio <= 0.1 && !this.$root.isVisibleVideoContainer) {
          // The embed wrapper is 90% or more hidden
          this.clickContainer(this.currentChannel.embedData.elementId, true);
        }
      });
    }, options);

    // Observe the embed wrapper
    if (this.$refs.embedWrapper) {
      observer.observe(this.$refs.embedWrapper);
    }

    // rootMargin: '0px 0px -100% 0px',
    //   threshold: 0


    this.displayChannels = this.settings.channels.filter(this.showChannel);

    this.setIsMobileDevice();
    this.currentChannel = this.displayChannels.find((item) => item.embedData);

    // setTimeout(() => {
    //   // window.scrollTo({
    //   //   top: refItem,
    //   //   behavior: "smooth",
    //   // });
    //
    //   if (this.currentChannel && this.currentChannel?.embedData?.elementId && this.$refs.embed) {
    //     this.clickContainer(this.currentChannel.embedData.elementId, true);
    // }
    // }, 1000);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.checkScrollPosition);
  },
  updated: function () {
    if (
      JSON.stringify(this.displayChannels) !=
      JSON.stringify(this.settings.channels.filter(this.showChannel))
    ) {
      this.displayChannels = this.settings.channels.filter(this.showChannel);
    }
  },
};
</script>