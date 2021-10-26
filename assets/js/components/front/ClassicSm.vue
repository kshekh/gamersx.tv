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
    <!-- <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="first()">
      <img
        alt="cursor-left"
        class="cursor-pointer"
        v-show="allowScrolling && rowIndex > 0"
        src="/images/left-arrow.png"
      />
    </div> -->
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
      <!-- test with width and height transition -->
      <!-- <div
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
          md:h-18
          xl:h-32
          transform
          transition-all
          hover:w-52
          hover:h-48
        "
      > -->
      <div
        ref="channelDivs"
        v-for="(channel, index) in displayChannels"
        :key="index"
        class="
          flex
          items-center
          mr-1.5
          xl:mr-3
          flex-shrink-0
          w-36
          md:w-28
          xl:w-48
          h-20
          md:h-18
          xl:h-32
        "
      >
        <component :is="channel.componentName" v-bind="channel" :cuttedBorder="true"></component>
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
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerClassicSm.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerClassic.vue";

import SliderArrow from "../helpers/SliderArrow.vue";
import PlayButton from "../helpers/PlayButton.vue";

import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "ClassicSm",
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-arrow": SliderArrow,
    "play-button": PlayButton,
  },
  props: {
    settings: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      rowIndex: 0,
      displayChannels: [],
      allowScrolling: false,
    };
  },
  methods: {
    showChannel(channel) {
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
  },
  mounted() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
  updated() {
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
  },
};
</script>
<style scoped></style>
