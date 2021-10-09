<template>
  <div
    :style="customBg"
    @swiped-left="forward()"
    @swiped-right="back()"
    class="home-row mb-7 md:mb-9 xl:mb-14"
  >
    <div class="container mx-auto">
      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="first()">
        <img
          alt="cursor-left"
          class="cursor-pointer"
          v-show="displayChannels.length > 1 && rowIndex > 0"
          src="/images/left-arrow.png"
        />
      </div>

      <div ref="channelBox" class="flex overflow-hidden">
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

      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="forward()">
        <img
          alt="cursor-right"
          class="cursor-pointer"
          v-show="displayChannels.length > 1"
          src="/images/right-arrow.png"
        />
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerFullWidthDescriptive.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainer.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "FullWidthDescriptive",
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
      displayChannels: []
    };
  },
  computed: {
    customBg: function() {
      let selected = this.displayChannels[this.rowIndex];
      if (selected && selected.customArt) {
        return {
          backgroundImage: "url(https://picsum.photos/2000/3000)"
          // backgroundImage: "url(" + selected.customArt + ")"
        };
      } else {
        return {};
      }
    }
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
    }
  },
  mounted: function() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  }
};
</script>
<style scoped></style>
