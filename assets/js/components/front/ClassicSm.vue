<template>
  <div @swiped-left="forward()" @swiped-right="back()">
    <h2
      class="
        text-white
        pl-8
        font-calibri font-bold
        text-sm
        md:text-2xl
        xl:text-4xl
        md:pl-10
        xl:pl-24
      "
    >
      {{ settings.title }}
      <title-addinional-description />
    </h2>
    <!-- <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="first()">
      <img
        alt="cursor-left"
        class="cursor-pointer"
        v-show="allowScrolling && rowIndex > 0"
        src="/images/left-arrow.png"
      />
    </div> -->
    <div ref="channelBox" class="flex overflow-hidden pt-3 md:pt-2 xl:pt-7 pb-7 md:pb-6 xl:pb-12 pl-4 xl:pl-20">
      <div
        ref="channelDivs"
        v-for="(channel, index) in displayChannels"
        :key="index"
        class="
          flex-shrink-0
          mr-1.5
          xl:mr-3
          w-36
          md:w-28
          xl:w-48
          h-20
          md:h-16
          xl:h-32
        "
      >
        <component :is="channel.componentName" v-bind="channel"></component>
      </div>
    </div>
    <!-- <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="forward()">
        <img
          alt="cursor-right"
          class="cursor-pointer"
          v-show="allowScrolling"
          src="/images/right-arrow.png"
        />
      </div> -->
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainerClassicSm.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "ClassicSm",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
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
      allowScrolling: false,
    };
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
  },
  mounted: function () {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
  updated: function () {
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
  },
};
</script>
<style scoped></style>
