<template>
  <div>
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
          cursor-default
          text-white
          font-calibri font-bold
          text-sm
          md:text-2xl
          xl:text-4xl
          mr-2
        "
      >
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
    <!-- <div class="w-16 h-16 shrink-0 grow-0" @click="first()">
      <img
        alt="cursor-left"
        class="cursor-pointer"
        v-show="allowScrolling && rowIndex > 0"
        src="/images/left-arrow.png"
      />
    </div> -->
    <div :class="{'relative':isMobileDevice,'flex':true}" style="align-items: center;">
      <div class="w5-center " ref="backArrow">
        <slider-arrow
          :isNext="false"
          :videoType="'twitch'"
          @arrow-clicked="back()"
        />
      </div>
      <div
        @mousemove="this.triggerDragging"
        v-on="!isMobileDevice ? { mousedown: this.startDragging } : {}"
        @mouseup="this.stopDragging"
        @mouseleave="this.stopDragging"
        @scroll="this.handleScroll"
        ref="channelBox"
        style="width: 100%"
        class="
        flex
        overflow-hidden custom-smooth-scroll
        pt-5
        xl:pt-9
        pb-7
        md:pb-6
        xl:pb-12
        pl-4
      "
      >
        <!-- test with width and height transition -->
        <!-- <div
          ref="channelDivs"
          v-for="(channel, index) in displayChannels"
          :key="index"
          class="
            shrink-0
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
          shrink-0
          w-36
          md:w-40
          lg:w-52
          xl:w-64
          h-20
          md:h-22
          lg:h-30
          xl:h-36
        "
        >
          <component :is="channel.componentName" v-bind="channel" :cuttedBorder="true"></component>
        </div>
      </div>
      <div class="w5-center" ref="forwardArrow" style="right: 0" :class="{ sliderArrowHide:!(this.displayChannels.length > 1) }">
        <slider-arrow
          :isNext="true"
          :videoType="'twitch'"
          @arrow-clicked="forward()"
        />
      </div>
    </div>
    <!-- <div class="w-16 h-16 shrink-0 grow-0" @click="forward()">
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
import embedMixin from "../../mixins/embedFrameMixin";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

require("swiped-events");

export default {
  name: "ClassicSm",
  mixins: [embedMixin],
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
      max_scroll_left: 0,
      isMobileDevice: false,
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
      let content_scroll_left = content.scrollLeft;
      content_scroll_left -= 300;
      if (content_scroll_left <= 0) {
        content_scroll_left = 0;
      }
      content.scrollLeft = content_scroll_left;
    },
    forward() {
      const content = this.$refs.channelBox
      const content_scroll_width = content.scrollWidth;
      let content_scroll_left = content.scrollLeft;
      content_scroll_left += 300;
      if (content_scroll_left >= content_scroll_width)
      {
        content_scroll_left = content_scroll_width;
      }
      content.scrollLeft = content_scroll_left;
    },
    reorder() {
      // this.$root.$emit('close-other-layouts');
      // for (let i = 0; i < this.$refs.channelDivs.length; i++) {
      //   let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
      //   // Add one to j because flexbox order should start with 1, not 0
      //   this.$refs.channelDivs[i].style.order = j + 1;
      // }
    },
    hideArrows(left = true,right = true){
      // console.log(this.$refs)
      if (right)
        this.$refs.forwardArrow.classList.add("sliderArrowHide")
      if (left)
        this.$refs.backArrow.classList.add("sliderArrowHide")
    },

    handleScroll () {
      if (this.$refs.channelBox.scrollLeft === this.max_scroll_left){
        this.$refs.forwardArrow.classList.add("sliderArrowHide")
      }
      else{
        this.$refs.forwardArrow.classList.remove("sliderArrowHide")
      }

      if (this.$refs.channelBox.scrollLeft > 0){
        this.$refs.backArrow.classList.remove("sliderArrowHide")
      }else
        this.$refs.backArrow.classList.add("sliderArrowHide")
    },
    setIsMobileDevice() {
      const checkDeviceType = navigator.userAgent.toLowerCase().match(/mobile/i);
      if(checkDeviceType) {
        this.isMobileDevice = true;
      } else {
        this.isMobileDevice = false;
      }
    },
  },
  mounted: function() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
    this.$refs.channelBox.addEventListener('scroll', this.handleScroll);
    this.$refs.channelBox.scrollLeft = 0;
    this.setIsMobileDevice();
  },
  updated: function() {
    if(JSON.stringify(this.displayChannels) !== JSON.stringify(this.settings.channels.filter(this.showChannel))){
      this.displayChannels = this.settings.channels.filter(this.showChannel);
    }
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
    this.max_scroll_left = this.$refs.channelBox.scrollWidth - this.$refs.channelBox.clientWidth;
    if (this.max_scroll_left === 0){
      this.hideArrows()
    }
    this.hideArrows(true,false)
    this.$refs.channelBox.scrollLeft = 0;
  },
};
</script>
<style scoped></style>
