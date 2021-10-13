<template>
  <div
    @swiped-left="forward()"
    @swiped-right="back()"
    class="home-row mb-7 md:mb-9 xl:mb-14 relative "
  >
    <div
      class="aspect-ratio-box container mx-auto px-4 pt-5 pb-2 md:pb-9 md:px-12"
    >
      <div class="flex items-center justify-between">
        <button class="text-purple" @click="back()">
          <svg
            class="h-4 w-5 md:h-5 md:w-7 xl:h-12 xl:w-8 fill-current transform rotate-180"
            viewBox="0 0 19 30"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M 19 14.999 L 0 30 L 0 0 L 19 14.999 Z" />
          </svg>
        </button>

        <div
          class="flex w-full items-end justify-between md:flex-col md:items-center"
        >
          <div ref="channelBox" class="mr-5 md:mr-0 md:mb-8 xl:mb-24">
            <div
              ref="channelDivs"
              v-for="(channel, index) in displayChannels"
              :key="index"
            >
              <component
                :is="channel.componentName"
                v-show="index === rowIndex"
                v-bind="channel"
              ></component>
            </div>
          </div>
          <div class="flex items-center space-x-1 md:space-x-2">
            <slider-dot
              v-for="(channel, index) in displayChannels"
              :key="'channelDot' + index"
              :embedType="channel.embedName"
              :dotIndex="index"
              :isDotActive="index === rowIndex"
              @slider-dot-clicked="setActiveChannel"
            />
          </div>
        </div>

        <button class="flex-shrink-0 text-purple" @click="forward()">
          <svg
            class="h-4 w-5 md:h-5 md:w-7 xl:h-12 xl:w-8 fill-current"
            viewBox="0 0 19 30"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M 19 14.999 L 0 30 L 0 0 L 19 14.999 Z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerFullWidthDescriptive.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainer.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import SliderDot from "../helpers/SliderDot.vue";

require("swiped-events");

export default {
  name: "FullWidthDescriptive",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-dot": SliderDot
  },
  props: {
    settings: {
      type: Object,
      required: true
    }
  },
  data: function() {
    return {
      rowIndex: 0,
      displayChannels: []
    };
  },
  methods: {
    showChannel: function(channel) {
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
    first: function() {
      this.rowIndex = 0;
      this.reorder();
    },
    back: function() {
      this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
      this.reorder();
    },
    forward: function() {
      this.rowIndex = (this.rowIndex + 1).mod(this.displayChannels.length);
      this.reorder();
    },
    reorder() {
      for (let i = 0; i < this.$refs.channelDivs.length; i++) {
        let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
        // Add one to j because flexbox order should start with 1, not 0
        this.$refs.channelDivs[i].style.order = j + 1;
      }
    },
    setActiveChannel(channelIndex) {
      this.rowIndex = channelIndex;
    }
  },
  mounted: function() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  }
};
</script>
<style scoped></style>
