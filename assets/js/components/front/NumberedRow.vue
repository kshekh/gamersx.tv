<template>
  <div @swiped-left="forward()" @swiped-right="back()">
    <div
      class="flex items-center justify-between pl-8 md:pl-10
          xl:pl-24 pr-4 md:pr-5 xl:pr-12"
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
      class="flex overflow-hidden pt-8 xl:pt-12 pb-9 md:pb-8 xl:pb-14 pl-4 xl:pl-20"
    >
      <div
        class="flex items-end"
        ref="channelDivs"
        v-for="(channel, index) in displayChannels"
        :key="index"
      >
        <span
          :data-number="index + 1"
          class="transform translate-x-3 leading-extra-tight md:translate-x-4 xl:translate-x-5 flex-shrink-0 font-bahnschrift font-semibold text-8xl md:text-xxl xl:text-3xxl text-stroke"
          :class="{
            'translate-x-8 md:translate-x-10 xl:translate-x-12': index + 1 >= 10
          }"
        >
          {{ index + 1 }}
        </span>
        <component
          :is="channel.componentName"
          v-bind="channel"
          class=""
        ></component>
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerNumbered.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerNumbered.vue";

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
      this.$root.$emit('close-other-layouts');
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
