<template>
  <div
    ref="embedWrapper"
    @swiped-left="forward()"
    @swiped-right="back()"
    @mouseenter="mouseEntered()"
    @mousemove="checkMouseActive()"
    class="home-row mb-7 md:mb-9 xl:mb-14 bg-cover bg-no-repeat relative min-h-mobile home-banner-section"
    :class="{'mobile-full-width': isMobileDevice}"
    :style="customBg"
  >
    <div class="container mx-auto">
      <div class="pb-50p"></div>
      <div
        class="px-4 md:px-12 flex items-center justify-between absolute inset-0 z-10"
      >
        <slider-arrow
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
            <slider-dot
              v-for="(channel, index) in displayChannels"
              :key="'channelDot' + index"
              :embedType="currentChannelEmbedName"
              :dotIndex="index"
              :isDotActive="index === rowIndex"
              @slider-dot-clicked="setActiveChannel"
            />
          </div>
        </div>

        <slider-arrow
          :isNext="true"
          :videoType="currentChannelEmbedName"
          @arrow-clicked="forward()"
        />
      </div>
    </div>
  </div>
</template>

<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerFullWidthDescriptive.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerDescriptive.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import SliderDot from "../helpers/SliderDot.vue";
import SliderArrow from "../helpers/SliderArrow.vue";

import isBoxInViewport from "../../mixins/isBoxInViewport";

require("swiped-events");

export default {
  name: "FullWidthDescriptive",
  mixins: [isBoxInViewport],
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-dot": SliderDot,
    "slider-arrow": SliderArrow,
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
      isMouseMovingTimeout: false,
      isMobileDevice: false
    };
  },
  computed: {
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
  },
  mounted() {
    if (!this.isRowFirst) {
      this.isAllowPlaying = false;
    } else {
      window.addEventListener("scroll", this.checkIfBoxInViewPort);
    }
    this.displayChannels = this.settings.channels.filter(this.showChannel);
    this.setIsMobileDevice();
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
