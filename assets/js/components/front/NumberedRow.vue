<template>
  <div @swiped-left="forward()" @swiped-right="back()" class="home-row p-6">
    <div
      class="flex items-center justify-between pl-3 mb-3 md:pl-6 xl:pl-5 md:mb-2 xl:mb-7"
    >
      <h2
        class="text-white   font-calibri font-bold text-sm  md:text-2xl xl:text-4xl"
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
    <div class="flex flex-row justify-start items-center">
      <div ref="channelBox" class="flex flex-row p-5 overflow-hidden">
        <div
          class="flex flex-row"
          ref="channelDivs"
          v-for="(channel, index) in displayChannels"
          :key="index"
        >
          <div class="font-extrabold big-number">
            {{ index + 1 }}
          </div>
          <component :is="channel.componentName" v-bind="channel"></component>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import SliderArrow from "../helpers/SliderArrow.vue";

require("swiped-events");

export default {
  name: "NumberedRow",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-arrow": SliderArrow
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
    clickPrev() {},
    clickNext() {}
  },
  mounted: function() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
  updated: function() {
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
  }
};
</script>
<style scoped></style>
