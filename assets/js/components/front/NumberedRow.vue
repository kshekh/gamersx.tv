<script setup>
import "swiped-events";
import { computed, defineAsyncComponent, onMounted, onUpdated, ref } from "vue";

import { useContainerStore } from "../stores/ContainerStore";
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerNumbered.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerNumbered.vue";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import SliderArrow from "../helpers/SliderArrow.vue";
import PlayButton from "../helpers/PlayButton.vue";

// Props
const props = defineProps({
  settings: {
    type: Object,
    required: true,
  },
});

const containerStore = useContainerStore();

// Data
const allowScrolling = ref(false);
const backArrow = ref(null);
const channelBox = ref(null);
const displayChannels = ref([]);
const forwardArrow = ref(null);
const isMobileDevice = ref(false);
const max_scroll_left = ref(0);
const mouseDown = ref(false);
const rowIndex = ref(0);
const scrollLeft = ref(0);
const startX = ref(0);

// Computed

const displayChannelNames = computed(() => {
  return displayChannels.value.map((channel, index) => {
    return defineAsyncComponent(() =>
      channel.componentName === "EmbedContainer"
        ? import("../layout/EmbedContainer/EmbedContainerNumbered.vue")
        : import("../layout/NoEmbedContainer/NoEmbedContainerNumbered.vue"),
    );
  });
});

// Methods
function showChannel(channel) {
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
}

function first() {
  rowIndex.value = 0;
  reorder();
}

function back() {
  // console.log("Back: ",this.rowIndex,this.displayChannels.length,(this.rowIndex + 1).mod(this.displayChannels.length))
  // this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
  // this.reorder();
  const content = channelBox.value;
  let content_scroll_left = content.scrollLeft;

  content_scroll_left -= 300;
  if (content_scroll_left <= 0) {
    content_scroll_left = 0;
  }

  content.scrollLeft = content_scroll_left;
}

function forward() {
  const content = channelBox.value;
  const content_scroll_width = content.scrollWidth;
  let content_scoll_left = content.scrollLeft;
  content_scoll_left += 300;
  if (content_scoll_left >= content_scroll_width) {
    content_scoll_left = content_scroll_width;
  }
  content.scrollLeft = content_scoll_left;
}

function reorder() {
  // this.$root.$emit('close-other-layouts');
  // for (let i = 0; i < this.$refs.channelDivs.length; i++) {
  //   let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
  //   // Add one to j because flexbox order should start with 1, not 0
  //   this.$refs.channelDivs[i].style.order = j + 1;
  // }
}

function hideArrows(left = true, right = true) {
  if (right) forwardArrow.value.classList.add("sliderArrowHide");
  if (left) backArrow.value.classList.add("sliderArrowHide");
}

function handleScroll() {
  if (channelBox.value.scrollLeft == max_scroll_left.value) {
    forwardArrow.value.classList.add("sliderArrowHide");
  } else {
    forwardArrow.value.classList.remove("sliderArrowHide");
  }

  if (channelBox.value.scrollLeft > 0) {
    backArrow.value.classList.remove("sliderArrowHide");
  } else backArrow.value.classList.add("sliderArrowHide");
}

function clickPrev() {}
function clickNext() {}

function setIsMobileDevice() {
  const checkDeviceType = navigator.userAgent.toLowerCase().match(/mobile/i);
  if (checkDeviceType) {
    isMobileDevice.value = true;
  } else {
    isMobileDevice.value = false;
  }
}

function startDragging(event) {
  if (containerStore.isMoveContainer) {
    return;
  }

  mouseDown.value = true;
  startX.value = event.pageX - channelBox.value.offsetLeft;
  scrollLeft.value = channelBox.value.scrollLeft;

  triggerDragging(event);
}

// Method to stop dragging
function stopDragging(event) {
  mouseDown.value = false;
}

// Method to handle dragging
function triggerDragging(event) {
  event.preventDefault();

  if (containerStore.isMoveContainer) {
    return;
  }

  if (!mouseDown.value) {
    return;
  }

  const x = event.pageX - channelBox.value.offsetLeft;
  const scroll = x - startX.value;
  channelBox.value.scrollLeft = scrollLeft.value - scroll;
}

// Lifecycle methods

onMounted(() => {
  console.log("channels", props.settings.channels);
  if (props.settings.channels.length) {
    displayChannels.value = props.settings.channels.filter(showChannel);
  }
  if (channelBox.value) {
    channelBox.value.addEventListener("scroll", handleScroll);
    channelBox.value.scrollLeft = 0;
  }
  setIsMobileDevice();
});

onUpdated(() => {
  if (
    JSON.stringify(displayChannels.value) !==
    JSON.stringify(props.settings.channels.filter(showChannel))
  ) {
    displayChannels.value = props.settings.channels.filter(showChannel);
  }
  allowScrolling.value =
    channelBox.value.scrollWidth > channelBox.value.clientWidth;
  max_scroll_left.value =
    channelBox.value.scrollWidth - channelBox.value.clientWidth;
  if (max_scroll_left.value === 0) {
    hideArrows();
  }
  hideArrows(true, false);
  channelBox.value.scrollLeft = 0;
});
</script>

<template>
  <div id="numberedRow">
    <div
      class="flex items-center justify-between pl-8 md:pl-10 xl:pl-24 pr-4 md:pr-5 xl:pr-12"
    >
      <h2
        class="cursor-default text-white font-calibri font-bold text-sm md:text-2xl xl:text-4xl mr-2"
      >
        {{ props.settings.title }}
        <TitleAdditionalDescription v-show="props.settings.onGamersXtv" />
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
    <div
      :class="{ relative: isMobileDevice, flex: true }"
      style="align-items: center"
    >
      <div class="w5-center" ref="backArrow">
        <SliderArrow
          :isNext="false"
          :videoType="'twitch'"
          @arrow-clicked="back"
        />
      </div>
      <div
        @mousemove="triggerDragging"
        v-on="!isMobileDevice ? { mousedown: startDragging } : {}"
        @mouseup="stopDragging"
        @mouseleave="stopDragging"
        @scroll="handleScroll"
        ref="channelBox"
        style="width: 100%"
        class="flex overflow-hidden custom-smooth-scroll pt-4 xl:pt-10 pb-6 md:pb-8 xl:pb-10 pl-4 xl:pl-0"
      >
        <div
          class="flex items-end"
          ref="channelDivs"
          v-for="(channel, index) in displayChannels"
          :key="index"
        >
          <span
            :data-number="index + 1"
            class="transform translate-x-3 leading-extra-tight md:translate-x-4 xl:translate-x-5 shrink-0 font-bahnschrift font-semibold text-8xl md:text-xxl xl:text-3xxl text-stroke"
            :class="{
              'translate-x-8 md:translate-x-10 xl:translate-x-12':
                index + 1 >= 10,
            }"
          >
            {{ index + 1 }}
          </span>
          <component
            :is="displayChannelNames[index]"
            v-bind="{ ...channel }"
            class=""
          ></component>
        </div>
      </div>
      <div
        class="w5-center"
        ref="forwardArrow"
        style="right: 0"
        :class="{ sliderArrowHide: !(displayChannels.length > 1) }"
      >
        <SliderArrow
          :isNext="true"
          :videoType="'twitch'"
          @arrow-clicked="forward"
        />
      </div>
    </div>
  </div>
</template>
