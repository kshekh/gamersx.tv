<template>
  <div @swiped-left="forward()" @swiped-right="back()">
    <div
      class="
        flex
        items-center
        justify-between
        pl-8
        md:pl-10
        xl:pl-24
        pr-4
        md:pr-5
        xl:pr-12
      "
    >
      <h2
        class="
          text-white
          font-calibri font-bold
          text-sm
          md:text-2xl
          xl:text-4xl
          mr-2
        "
      >
        {{ settings.title }}
        <title-addinional-description />
      </h2>
      <div class="flex items-center space-x-5">
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
      class="
        flex
        overflow-hidden
        pt-5
        xl:pt-9
        pb-7
        md:pb-6
        xl:pb-12
        pl-4
        xl:pl-20
      "
    >
      <div
        ref="channelDivs"
        v-for="(channel, index) in displayChannels"
        :key="index"
        class="
          flex
          items-center
          flex-shrink-0
          mr-3
          md:mr-2
          xl:mr-4
          w-64
          md:w-36
          xl:w-64
          h-48
          md:h-28
          xl:h-48
        "
      >
        <component :is="channel.componentName" v-bind="channel" :cuttedBorder="true"></component>
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerClassicMd.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerClassic.vue";

import SliderArrow from "../helpers/SliderArrow.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "ClassicMd",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-arrow": SliderArrow,
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
    back() {
      this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
      this.reorder();
    },
    forward: function() {
      this.rowIndex = (this.rowIndex + 1).mod(this.displayChannels.length);
      this.reorder();
    },
    reorder() {
      this.$root.$emit('close-other-layouts');
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
