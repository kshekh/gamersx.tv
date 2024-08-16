<script setup>
import {
  computed,
  defineAsyncComponent,
  ref,
  reactive,
  onMounted,
  onUnmounted,
  onUpdated,
} from "vue";
import SliderArrow from "../helpers/SliderArrow.vue";
import embedMixin from "../../mixins/embedFrameMixin";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import "swiped-events";
import { useContainerStore } from "../stores/containerStore";

const props = defineProps({
  settings: {
    type: Object,
    required: false,
  },
});

const containerStore = useContainerStore();

const allowScrolling = ref(false);
const backArrow = ref(null);
const channelBox = ref(null);
const displayChannels = ref([]);
const forwardArrow = ref(null);
const isMobileDevice = ref(false);
const maxScrollLeft = ref(0);
const mouseDown = ref(false);
const rowIndex = ref(0);
const scrollLeft = ref(0);
const startX = ref(0);
console.log("display channels", displayChannels.value);
const showChannel = (channel) => {
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
};

const first = () => {
  rowIndex.value = 0;
  // reorder();
};

const backward = () => {
  const content = channelBox.value;
  let content_scroll_left = content.scrollLeft;
  content_scroll_left -= 300;
  if (content_scroll_left <= 0) {
    content_scroll_left = 0;
  }
  content.scrollLeft = content_scroll_left;
};

const forward = () => {
  const content = channelBox.value;
  const content_scroll_width = content.scrollWidth;
  let content_scroll_left = content.scrollLeft;
  content_scroll_left += 300;
  if (content_scroll_left >= content_scroll_width) {
    content_scroll_left = content_scroll_width;
  }
  content.scrollLeft = content_scroll_left;
};

const hideArrows = (left = true, right = true) => {
  if (left) backArrow.value.classList.add("sliderArrowHide");
  if (right) forwardArrow.value.classList.add("sliderArrowHide");
};

const handleScroll = () => {
  if (channelBox.value.scrollLeft === maxScrollLeft.value) {
    forwardArrow.value.classList.add("sliderArrowHide");
  } else {
    forwardArrow.value.classList.remove("sliderArrowHide");
  }

  if (channelBox.value.scrollLeft > 0) {
    backArrow.value.classList.remove("sliderArrowHide");
  } else {
    backArrow.value.classList.add("sliderArrowHide");
  }
};

const setIsMobileDevice = () => {
  isMobileDevice.value = navigator.userAgent.toLowerCase().includes("mobile");
};

onMounted(() => {
  displayChannels.value = props.settings.channels.filter(showChannel);
  console.log("display channels ", displayChannels.value);
  if (channelBox.value) {
    channelBox.value.addEventListener("scroll", handleScroll);
    channelBox.value.scrollLeft = 0;
  }
  setIsMobileDevice();
});

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

// Computed

const displayChannelNames = computed(() => {
  return displayChannels.value.map((channel, index) => {
    return defineAsyncComponent(() =>
      channel.componentName === "EmbedContainer"
        ? import("../layout/EmbedContainer/EmbedContainerClassicLg.vue")
        : import("../layout/NoEmbedContainer/NoEmbedContainerClassic.vue"),
    );
  });
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
  maxScrollLeft.value =
    channelBox.value.scrollWidth - channelBox.value.clientWidth;
  if (maxScrollLeft.value === 0) {
    hideArrows();
  }
  hideArrows(true, false);
  channelBox.value.scrollLeft = 0;
});

onUnmounted(() => {
  if (channelBox.value) {
    channelBox.value.removeEventListener("scroll", handleScroll);
  }
});
</script>

<template>
  <div>
    <div
      class="flex items-center justify-between pl-8 md:pl-10 shrink-0 mr-3 xl:pl-24 pr-4 md:pr-5 xl:pr-12"
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
      <div class="w5-center sliderArrowHide" ref="backArrow">
        <SliderArrow
          :isNext="false"
          :videoType="'twitch'"
          @arrow-clicked="backward"
        />
      </div>
      <div
        v-on="!isMobileDevice ? { mousedown: startDragging } : {}"
        @mousemove="triggerDragging"
        @mouseup="stopDragging"
        @mouseleave="stopDragging"
        @scroll="handleScroll"
        ref="channelBox"
        style="width: 100%"
        class="flex overflow-hidden custom-smooth-scroll pt-5 xl:pt-9 pb-7 md:pb-6 xl:pb-12 pl-4"
      >
        <div
          v-for="(channel, index) in displayChannels"
          :key="index"
          ref="channelDivs"
          class="flex items-center shrink-0 mr-3 md:mr-2 xl:mr-4 w-80 md:w-72 xl:w-96 h-45 md:h-40 xl:h-54"
        >
          <component
            :is="displayChannelNames[index]"
            v-bind="{
              ...channel,
              cuttedBorder: true,
            }"
          />
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

<!-- <script>
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerClassicLg.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerClassic.vue";

import SliderArrow from "../helpers/SliderArrow.vue";
import embedMixin from "../../mixins/embedFrameMixin";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";

import "swiped-events";

export default {
  name: "ClassicLg",
  mixins: [embedMixin],
  components: {
    EmbedContainer: EmbedContainer,
    NoEmbedContainer: NoEmbedContainer,
    "title-additional-description": TitleAdditionalDescription,
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
      const content = this.$refs.channelBox;
      let content_scoll_left = content.scrollLeft;
      content_scoll_left -= 300;
      if (content_scoll_left <= 0) {
        content_scoll_left = 0;
      }
      content.scrollLeft = content_scoll_left;
    },
    forward() {
      const content = this.$refs.channelBox;
      const content_scroll_width = content.scrollWidth;
      let content_scroll_left = content.scrollLeft;
      content_scroll_left += 300;
      if (content_scroll_left >= content_scroll_width) {
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
    hideArrows(left = true, right = true) {
      // console.log(this.$refs)
      if (right) this.$refs.forwardArrow.classList.add("sliderArrowHide");
      if (left) this.$refs.backArrow.classList.add("sliderArrowHide");
    },
    handleScroll() {
      if (this.$refs.channelBox.scrollLeft === this.max_scroll_left) {
        this.$refs.forwardArrow.classList.add("sliderArrowHide");
      } else {
        this.$refs.forwardArrow.classList.remove("sliderArrowHide");
      }

      if (this.$refs.channelBox.scrollLeft > 0) {
        this.$refs.backArrow.classList.remove("sliderArrowHide");
      } else this.$refs.backArrow.classList.add("sliderArrowHide");
    },
    setIsMobileDevice() {
      const checkDeviceType = navigator.userAgent
        .toLowerCase()
        .match(/mobile/i);
      if (checkDeviceType) {
        this.isMobileDevice = true;
      } else {
        this.isMobileDevice = false;
      }
    },
  },
  mounted() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
    this.$refs.channelBox.addEventListener("scroll", this.handleScroll);
    this.$refs.channelBox.scrollLeft = 0;
    this.setIsMobileDevice();
  },
  updated: function () {
    if (
      JSON.stringify(this.displayChannels) !==
      JSON.stringify(this.settings.channels.filter(this.showChannel))
    ) {
      this.displayChannels = this.settings.channels.filter(this.showChannel);
    }
    this.allowScrolling =
      this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
    this.max_scroll_left =
      this.$refs.channelBox.scrollWidth - this.$refs.channelBox.clientWidth;
    if (this.max_scroll_left === 0) {
      this.hideArrows();
    }
    this.hideArrows(true, false);
    this.$refs.channelBox.scrollLeft = 0;
  },
};
</script> -->
