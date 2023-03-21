<template>
  <div
    @swiped-left="forward()"
    @swiped-right="back()"
    class="
      mb-7
      md:mb-9
      xl:mb-14
      bg-cover bg-no-repeat
      relative
      overflow-hidden
      min-h-mobile
    "
    :style="customBg"
  >
    <div class="container mx-auto">
      <div class="pb-50p"></div>
      <div
        class="
          pr-4
          md:pr-12
          py-2.5
          md:py-6
          xl:py-11
          flex
          items-center
          justify-between
          absolute
          inset-0
          z-10
        "
      >
        <div class="flex w-full h-full items-center justify-between">
          <div
            ref="channelBox"
            class="            
              py-2.5
              sm:py-0            
              sm:mr-20
              w-full            
              h-full
              flex flex-col
              justify-center
            "
          >
            <div
              ref="channelDivs"
              v-for="(channel, index) in displayChannels"
              :key="index"
              :class="{ 'h-full': index === rowIndex }"
            >
              <component
                :is="channel.componentName"
                v-show="index === rowIndex"
                v-bind="channel"
                @hide-controls="toggleControls(false)"
                @show-controls="toggleControls(true)"
              ></component>
            </div>
          </div>
          <div
            v-show="isControlsShown"
            class="
              flex
              items-center
              space-x-1
              md:space-x-2
              relative
              z-10
              self-end
            "
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

        <div
          v-show="isControlsShown"
          class="pr-8 sm:pr-0 ml-2 md:ml-5 xl:ml-8 flex-shrink-0 relative z-10 flex"
        >
         <slider-arrow-big
          :isNext="false"
          :videoType="currentChannelEmbedName"
          @arrow-big-clicked="back()"
        />
         <slider-arrow-big
          :isNext="true"
          :videoType="currentChannelEmbedName"
          @arrow-big-clicked="forward()"
        />

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerFullWidthImagery.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerImagery.vue";

import SliderDot from "../helpers/SliderDot.vue";
import SliderArrowBig from "../helpers/SliderArrowBig.vue";

require("swiped-events");

export default {
  name: "FullWidthImagery",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "slider-dot": SliderDot,
    "slider-arrow-big": SliderArrowBig
  },
  props: {
    settings: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {
      rowIndex: 0,
      displayChannels: [],
      isControlsShown: true,
    };
  },
  computed: {
    customBg: function () {
      let selected = this.displayChannels[this.rowIndex];
      if (selected && selected.customArt) {
        return {
          // backgroundImage: "url(https://picsum.photos/2000/3000)"
          backgroundImage: "url(" + selected.customArt + ")",
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
      for (let i = 0; i < this.$refs.channelDivs.length; i++) {
        let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
        // Add one to j because flexbox order should start with 1, not 0
        this.$refs.channelDivs[i].style.order = j + 1;
      }
    },
    setActiveChannel(channelIndex) {
      this.rowIndex = channelIndex;
    },
    toggleControls(val) {
      this.isControlsShown = val;
    },
  },
  mounted: function () {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
};
</script>
<style scoped></style>
