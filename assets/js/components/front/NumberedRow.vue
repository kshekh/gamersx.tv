<template>
  <div
    @swiped-left="forward()"
    @swiped-right="back()"
    class="mb-7 md:mb-9 xl:mb-14 pl-4 xl:pl-16"
  >
    <div
      class="flex items-center justify-between px-3 mb-3 md:px-6 xl:px-5 md:mb-2 xl:mb-7"
    >
      <h2
        class="text-white font-calibri font-bold text-sm  md:text-2xl xl:text-4xl"
      >
        {{ settings.title }}
        <title-addinional-description />
      </h2>
      <div class="flex items-center space-x-3 space-x-5">
        <slider-arrow
          :isNext="false"
          :videoType="'twitch'"
          @arrow-clicked="back()"
        />
        <slider-arrow
          :isNext="true"
          :videoType="'twitch'"
          @arrow-clicked="forward()"
        />
      </div>
    </div>
    <div
      ref="channelBox"
      class="flex space-x-3 md:space-x-1 xl:space-x-5 overflow-hidden"
    >
      <div
        class="flex items-baseline"
        ref="channelDivs"
        v-for="num of 12"
        :key="num"
      >
        <span
          :data-number="num"
          class="transform translate-x-3 md:translate-x-4 xl:translate-x-5 flex-shrink-0 font-bahnschrift font-semibold text-8xl md:text-xxl xl:text-3xxl text-stroke"
          :class="{
            'translate-x-8 md:translate-x-10 xl:translate-x-12': num >= 10
          }"
        >
          {{ num }}
        </span>
        <div
          class="flex-shrink-0 w-18 h-24 md:w-24 md:h-32 xl:w-28 xl:h-40 relative z-10"
        >
          <div class="relative">
            <play-button
              :videoType="'twitch'"
              class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
            />
            <img src="https://picsum.photos/200/300" alt="" />
          </div>
        </div>
      </div>
      <!-- <div
        class="flex px-2 md:px-2 xl:px-6"
        ref="channelDivs"
        v-for="(channel, index) in displayChannels"
        :key="index"
      >
        <span
          class="font-bahnschrift font-semibold text-8xl md:text-xxl xl:text-3xxl text-stroke"
        >
          {{ index + 1 }}
        </span>
        <component
          :is="channel.componentName"
          v-bind="channel"
          class=""
        ></component>
      </div> -->
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import SliderArrow from "../helpers/SliderArrow.vue";
import PlayButton from "../helpers/PlayButton.vue";

require("swiped-events");

export default {
  name: "NumberedRow",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-arrow": SliderArrow,
    "play-button": PlayButton
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
      displayChannels: [],
      allowScrolling: false
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
    first() {
      this.rowIndex = 0;
      this.reorder();
    },
    back() {
      this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
      this.reorder();
    },
    forward() {
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
    clickPrev() {},
    clickNext() {}
  },
  mounted() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
  updated() {
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
  }
};
</script>
