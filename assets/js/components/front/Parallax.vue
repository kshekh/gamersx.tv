<script setup>
import "swiped-events";
import { computed, onMounted, onUpdated, ref } from "vue";

import { useContainerStore } from "../stores/ContainerStore";
import SliderArrow from "../helpers/SliderArrow.vue";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

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
        ? import("../layout/EmbedContainer/EmbedContainerParallax.vue")
        : import("../layout/NoEmbedContainer/NoEmbedContainerParallax.vue"),
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
  let content_scoll_left = content.scrollLeft;
  content_scoll_left -= 300;
  if (content_scoll_left <= 0) {
    content_scoll_left = 0;
  }
  content.scrollLeft = content_scoll_left;
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
  // console.log(this.$refs)
  if (right) forwardArrow.value.classList.add("sliderArrowHide");
  if (left) backArrow.value.classList.add("sliderArrowHide");
}

function handleScroll() {
  if (containerStore.isMoveContainer) {
    return;
  }
  if (channelBox.value.scrollLeft == max_scroll_left.value) {
    forwardArrow.value.classList.add("sliderArrowHide");
  } else {
    forwardArrow.value.classList.remove("sliderArrowHide");
  }

  if (channelBox.value.scrollLeft > 0) {
    backArrow.value.classList.remove("sliderArrowHide");
  } else backArrow.value.classList.add("sliderArrowHide");
}
function customBg(channel) {
  if (channel.customArt) {
    return {
      backgroundImage: "url(" + channel.customArt + ")",
    };
  }
}

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

// Lifecycle Hooks

onMounted(() => {
  // Check if props.settings.channels has items
  if (props.settings.channels.length) {
    displayChannels.value = props.settings.channels.filter(showChannel.value);
  }
  // Check if the channelBox ref is attached properly
  if (channelBox.value) {
    channelBox.value.addEventListener("scroll", handleScroll);
    channelBox.value.scrollLeft = 0;
  }
  setIsMobileDevice();
});

onUpdated(() => {
  if (
    JSON.stringify(displayChannels.value) !=
    JSON.stringify(props.settings.channels.filter(showChannel.value))
  ) {
    displayChannels.value = props.settings.channels.filter(showChannel.value);
  }
  allowScrolling.value =
    channelBox.value.scrollWidth > channelBox.value.clientWidth;
  max_scroll_left.value =
    channelBox.value.scrollWidth - channelBox.value.clientWidth;
  if (max_scroll_left.value == 0) {
    hideArrows();
  }
  hideArrows(true, false);
  channelBox.value.scrollLeft = 0;
});
</script>

<template>
  <div>
    <div
      class="flex items-center justify-between pl-8 md:pl-10 xl:pl-24 pr-4 md:pr-5 xl:pr-12"
    >
      <h2
        class="cursor-default text-white font-calibri font-bold text-sm md:text-2xl xl:text-4xl mr-2"
      >
        {{ settings.title }}
        <TitleAdditionalDescription v-show="settings.onGamersXtv" />
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
      <div ref="backArrow" class="w5-center">
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
        ref="channelBox"
        style="width: 100%"
        class="flex overflow-hidden w-full custom-smooth-scroll pt-10 md:pt-16 lg:pt-12 pb-12 md:pb-14 lg:pb-18"
      >
        <div
          v-for="(channel, index) in displayChannels"
          :key="index"
          ref="channelDivs"
          :style="customBg(channel)"
          class="shrink-0 bg-cover bg-no-repeat bg-center w-90 h-30 xl:w-150 xl:h-50 bg-black"
        >
          <component
            :is="displayChannelNames[index]"
            v-bind="{ ...channel }"
          ></component>
        </div>
      </div>
      <div
        :class="{ sliderArrowHide: !(displayChannels.length > 1) }"
        ref="forwardArrow"
        class="w5-center"
        style="right: 0"
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
