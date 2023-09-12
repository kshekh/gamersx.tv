<template>
  <div>
    <div class="
          flex
          items-center
          justify-between
          pl-8
          md:pl-10 shrink-0 mr-3
          xl:pl-24
          pr-4
          md:pr-5
          xl:pr-12
          custom-row-padding
        ">
      <h2 class="
            text-white
            font-calibri font-bold
            text-sm
            md:text-2xl
            xl:text-4xl
            mr-2
          ">
        {{ settings.title }}
        <title-addinional-description v-show="settings.onGamersXtv" />
      </h2>
      <!--      <div class="flex items-center space-x-5">-->
      <!--        <slider-arrow-->
      <!--          :isNext="false"-->
      <!--          :videoType="'twitch'"-->
      <!--          @arrow-clicked="back()"-->
      <!--        />-->
      <!--        <slider-arrow-->
      <!--          :isNext="true"-->
      <!--          :videoType="'twitch'"-->
      <!--          @arrow-clicked="forward()"-->
      <!--        />-->
      <!--      </div>-->
    </div>

    <div class="flex" style="align-items: center;">

      <div class="w5-center sliderArrowHide" ref="backArrow">
        <slider-arrow :isNext="false" :videoType="'twitch'" @arrow-clicked="back()" />
      </div>
      <div @mousedown="this.startDragging" @mousemove="this.triggerDragging" @mouseup="this.stopDragging"
        @mouseleave="this.stopDragging" @scroll="this.handleScroll" ref="channelBox" style="width: 100%" class="
          flex
          overflow-hidden custom-smooth-scroll
          pt-5
          xl:pt-9
          pb-7
          md:pb-6
          xl:pb-12
          pl-4
        ">
        <div ref="channelDivs" v-for="(channel, index) in displayChannels" :key="index" class="
            flex
            items-center
            shrink-0
            mr-3
            md:mr-2
            xl:mr-4
            w-80
            md:w-72
            xl:w-96
            h-45
            md:h-40
            xl:h-54
          ">
          <component :is="channel.componentName" v-bind="channel" :cuttedBorder="true"></component>
        </div>
      </div>
      <div class="w5-center" ref="forwardArrow" style="right: 0"
        :class="{ sliderArrowHide: !(this.displayChannels.length > 1) }">
        <slider-arrow :isNext="true" :videoType="'twitch'" @arrow-clicked="forward()" />
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerClassicLg.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerClassic.vue";

import SliderArrow from "../helpers/SliderArrow.vue";
import embedMixin from "../../mixins/embedFrameMixin";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "ClassicLg",
  mixins: [embedMixin],
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-addinional-description": TitleAdditionalDescription,
    "slider-arrow": SliderArrow,
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
      max_scroll_left: 0,
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
      // console.log("Back: ",this.rowIndex,this.displayChannels.length,(this.rowIndex + 1).mod(this.displayChannels.length))
      // this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
      // this.reorder();
      const content = this.$refs.channelBox
      let content_scoll_left = content.scrollLeft;
      content_scoll_left -= 300;
      if (content_scoll_left <= 0) {
        content_scoll_left = 0;
      }
      content.scrollLeft = content_scoll_left;
    },
    forward() {
      const content = this.$refs.channelBox
      const content_scroll_width = content.scrollWidth;
      let content_scoll_left = content.scrollLeft;
      content_scoll_left += 300;
      if (content_scoll_left >= content_scroll_width) {
        content_scoll_left = content_scroll_width;
      }
      content.scrollLeft = content_scoll_left;
    },
    reorder() {
      // this.$root.$emit('close-other-layouts');
      // for (let i = 0; i < this.$refs.channelDivs.length; i++) {
      //   let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
      //   // Add one to j because flexbox order should start with 1, not 0
      //   this.$refs.channelDivs[i].style.order = j + 1;
      // }
    },
    hideArrows(left = true, right = true) {
      // console.log(this.$refs)
      if (right)
        this.$refs.forwardArrow.classList.add("sliderArrowHide")
      if (left)
        this.$refs.backArrow.classList.add("sliderArrowHide")
    },
    handleScroll() {
      if (this.$refs.channelBox.scrollLeft == this.max_scroll_left) {
        this.$refs.forwardArrow.classList.add("sliderArrowHide")
      }
      else {
        this.$refs.forwardArrow.classList.remove("sliderArrowHide")
      }

      if (this.$refs.channelBox.scrollLeft > 0) {
        this.$refs.backArrow.classList.remove("sliderArrowHide")
      } else
        this.$refs.backArrow.classList.add("sliderArrowHide")
      this.$root.$emit('close-other-layouts');
    }
  },
  mounted() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
    this.$refs.channelBox.addEventListener('scroll', this.handleScroll);
    this.$refs.channelBox.scrollLeft = 0;
  },
  updated: function () {
    if(JSON.stringify(this.displayChannels) != JSON.stringify(this.settings.channels.filter(this.showChannel))){
      this.displayChannels = this.settings.channels.filter(this.showChannel);
    }
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
    this.max_scroll_left = this.$refs.channelBox.scrollWidth - this.$refs.channelBox.clientWidth;
    if (this.max_scroll_left == 0) {
      this.hideArrows()
    }
    this.hideArrows(true, false)
    this.$refs.channelBox.scrollLeft = 0;
  },
};
</script>
<style scoped></style>
