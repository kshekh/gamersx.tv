<template>
  <div @swiped-left="forward()" @swiped-right="back()" class="home-row p-6">
    <h2
      class="text-white pl-3 mb-3  font-calibri font-bold text-sm  md:text-2xl xl:text-4xl md:pl-6 xl:pl-5 md:mb-2 xl:mb-7"
    >
      {{ settings.title }}
      <title-addinional-description />
    </h2>
    <div class="flex flex-row justify-start items-center">
      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="first()">
        <img
          alt="cursor-left"
          class="cursor-pointer"
          v-show="allowScrolling && rowIndex > 0"
          src="/images/left-arrow.png"
        />
      </div>
      <div ref="channelBox" class="flex flex-row p-5 overflow-hidden">
        <div
          ref="channelDivs"
          v-for="(channel, index) in displayChannels"
          :key="index"
        >
          <component :is="channel.componentName" v-bind="channel"></component>
        </div>
      </div>
      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="forward()">
        <img
          alt="cursor-right"
          class="cursor-pointer"
          v-show="allowScrolling"
          src="/images/right-arrow.png"
        />
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "ClassicMd",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription
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
    }
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
