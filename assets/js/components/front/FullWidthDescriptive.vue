<script setup>
import {
  defineProps,
  ref,
  onMounted,
  onBeforeUnmount,
  onUpdated,
  computed,
  watch,
} from "vue";
import { useContainerStore } from "../stores/containerStore";
import { useVideoStore } from "../stores/VideoStore";
import EmbedContainer from "../layout/EmbedContainer/EmbedContainerFullWidthDescriptive.vue";
import NoEmbedContainer from "../layout/NoEmbedContainer/NoEmbedContainerDescriptive.vue";
import TitleAdditionalDescription from "../singletons/TitleAdditionalDescription.vue";
import CommonContainer from "../layout/CommonContainer/CommonContainer.vue";
import SliderDot from "../helpers/SliderDot.vue";
import SliderArrow from "../helpers/SliderArrow.vue";
import TwitchEmbed from "../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../embeds/YouTubeEmbed.vue";

import "swiped-events";

const props = defineProps({
  rowPosition: Number,
  settings: Object,
});

const baseCoordinates = ref({});
const embedWrapper = ref(null);
const channelBox = ref(null);
const channelDivs = ref(null);
const containerWrapper = ref(null);
const currentChannel = ref(null);
const displayChannels = ref([]);
const isAllowPlaying = ref(true);
const isEmbedVisible = ref(false);
const isFirstVideoLoaded = ref(false);
const isMobileDevice = ref(false);
const isMouseMovingTimeout = ref(0);
const isMouseStopped = ref(false);
const isScrolledIn = ref(false);
const isShowTwitchEmbed = ref(false);
const rowIndex = ref(0);
const sliderDotRef = ref(null);

const videoStore = useVideoStore();

// Computed properties
const currentChannelEmbed = computed(() => {
  let selected = displayChannels.value && displayChannels.value[rowIndex.value];
  return selected || "TwitchEmbed";
});

const currentChannelEmbedName = computed(() => {
  let selected = displayChannels.value[rowIndex.value];
  return selected?.embedName || "TwitchEmbed";
});

const customBg = computed(() => {
  let selected = displayChannels.value[rowIndex.value];
  return selected?.customArt
    ? {
        backgroundImage: "url(" + selected.customArt + ")",
        backgroundSize: "100% 100%",
      }
    : {};
});

const embedSize = computed(() => {
  let width = window.innerWidth > 1279 ? 400 : 355;
  let height = window.innerWidth > 1279 ? 350 : 311;

  return {
    width: width + "px",
    height: height + "px",
  };
});

const isRowFirst = computed(() => props.rowPosition === 0);

const showArt = computed(() => {
  return (
    (currentChannel.value?.showOnline &&
      currentChannel.value?.onlineDisplay.showArt) ||
    (!currentChannel.value?.showOnline &&
      currentChannel.value?.offlineDisplay.showArt)
  );
});

const showEmbed = computed(() => {
  return (
    (currentChannel.value?.showOnline &&
      currentChannel.value?.onlineDisplay.showEmbed) ||
    (!currentChannel.value?.showOnline &&
      currentChannel.value?.offlineDisplay.showEmbed)
  );
});

const showOverlay = computed(() => {
  return (
    currentChannel.value?.overlay &&
    ((currentChannel.value?.showOnline &&
      currentChannel.value?.onlineDisplay.showOverlay) ||
      (!currentChannel.value?.showOnline &&
        currentChannel.value?.offlineDisplay.showOverlay))
  );
});

// Watchers
watch(isEmbedVisible, (isVisible) => {
  if (isVisible) {
    handleEmbedUpdate();
  } else {
    videoStore.resetStyles();
  }
});

watch(isScrolledIn, (scrollStatus) => {
  if (scrollStatus) {
    scrollIn();
  } else {
    scrollOut();
  }
});

function handleEmbedUpdate() {
  videoStore.resetStyles();
  // videoStore.resetEmbed(containerWrapper.value);

  if (!videoStore.activeEmbedIsEmpty) {
    videoStore.setStyles();
  }
}

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

function backward() {
  rowIndex.value = (rowIndex.value - 1).mod(displayChannels.value.length);
  reorder();
}

function forward() {
  rowIndex.value = (rowIndex.value + 1).mod(displayChannels.value.length);
  reorder();
}

function reorder() {
  checkMouseActive();
  for (let i = 0; i < channelDivs.value.length; i++) {
    let j = (i - rowIndex.value).mod(channelDivs.value.length);
    channelDivs.value[i].style.order = j + 1;
  }
}

function activateMouseStopped() {
  isMouseStopped.value = true;
}

function handleFirstVideoLoaded() {
  isFirstVideoLoaded.value = true;
}

function checkMouseActive() {
  isMouseStopped.value = false;
  clearTimeout(isMouseMovingTimeout.value);
  isMouseMovingTimeout.value = setTimeout(() => {
    isMouseStopped.value = true;
  }, 3000);
}

function mouseEntered() {
  if (isScrolledIn.value) {
    isAllowPlaying.value = true;
  }
}

function setActiveChannel(channelIndex) {
  rowIndex.value = channelIndex;
}

function scrollIn() {
  isEmbedVisible.value = false;
  isAllowPlaying.value = true;

  if (!videoStore.activeEmbedIsEmpty) {
    videoStore.clearExistingEmbed();
  }
}

function scrollOut() {
  isAllowPlaying.value = false;
  isEmbedVisible.value = true;
  isMouseStopped.value = false;

  videoStore.storeEmbed(currentChannel.value.embedData);

  clearTimeout(isMouseMovingTimeout.value);
}

function setIsMobileDevice() {
  isMobileDevice.value = !!navigator.userAgent.toLowerCase().match(/mobile/i);
}

function setBaseCoordinates() {
  baseCoordinates.value = embedWrapper.value.getBoundingClientRect();
}

// Lifecycle hooks
onMounted(() => {
  const refItem = sliderDotRef.value.getBoundingClientRect().top;
  console.log("common container ", containerWrapper.value);
  // Set up the Intersection Observer directly
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        isScrolledIn.value =
          entry.isIntersecting && entry.intersectionRatio > 0.1;
      });
    },
    {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    },
  );

  // Observe the embed wrapper
  if (embedWrapper.value) {
    observer.observe(embedWrapper.value);
  }

  // If it isn't the first row, then don't allow the video to play
  if (!isRowFirst.value) {
    isAllowPlaying.value = false;
  }

  displayChannels.value = props.settings.channels.filter(showChannel);

  setIsMobileDevice();
  currentChannel.value = displayChannels.value.find((item) => item.embedData);
});

onUpdated(() => {
  if (
    JSON.stringify(displayChannels.value) !==
    JSON.stringify(props.settings.channels.filter(showChannel))
  ) {
    displayChannels.value = props.settings.channels.filter(showChannel);
  }
});

onBeforeUnmount(() => {
  observer.disconnect();
});
</script>

<template>
  <div
    ref="embedWrapper"
    @swiped-left="forward"
    @swiped-right="backward"
    @mousemove="checkMouseActive"
    class="home-row mb-7 md:mb-9 xl:mb-14 bg-cover bg-no-repeat relative min-h-mobile home-banner-section"
    style="will-change: transform"
    :class="{ 'mobile-full-width': isMobileDevice }"
    :style="customBg"
  >
    <div class="container mx-auto">
      <div class="pb-50p"></div>
      <div
        class="px-4 md:px-12 flex items-center justify-between absolute inset-0 z-10"
      >
        <SliderArrow
          :isNext="false"
          :videoType="currentChannelEmbedName"
          @arrow-clicked="backward"
        />

        <div
          class="py-2 md:pt-8 xl:py-14 flex-grow min-w-0 flex h-full items-center justify-between md:flex-col"
        >
          <div
            ref="channelBox"
            class="mr-5 md:mr-0 md:pt-4 w-full flex-grow flex flex-col justify-center min-h-mobile-description"
          >
            <div
              ref="channelDivs"
              v-for="(channel, index) in displayChannels"
              :key="index"
            >
              <!--channel.componentName possible values: EmbedContainer-->
              <component
                :is="channel.componentName"
                v-if="index === rowIndex"
                v-bind="channel"
                :isAllowPlaying="isAllowPlaying"
                :isRowFirst="isRowFirst"
                :isFirstVideoLoaded="isFirstVideoLoaded"
                :isMouseStopped="isMouseStopped"
                :customBg="customBg"
                @first-video-buffered="handleFirstVideoLoaded"
                @activate-mouse-stopped="activateMouseStopped"
                @reset-mouse-moving="checkMouseActive"
              ></component>
            </div>
          </div>
          <div
            class="flex items-center space-x-1 md:space-x-2 z-10 self-end md:self-center absolute -bottom-[30px]"
          >
            <div
              ref="sliderDotRef"
              class="flex items-center space-x-1 md:space-x-2 z-10 self-end md:self-center absolute -bottom-[30px]"
            >
              <SliderDot
                v-for="(channel, index) in displayChannels"
                :key="'channelDot' + index"
                :embedType="currentChannelEmbedName"
                :dotIndex="index"
                :isDotActive="index === rowIndex"
                @slider-dot-clicked="setActiveChannel"
              />
            </div>
          </div>
        </div>

        <SliderArrow
          :isNext="true"
          :videoType="currentChannelEmbedName"
          @arrow-clicked="forward()"
        />
      </div>
    </div>

    <Teleport to="body">
      <div
        v-if="
          showEmbed &&
          currentChannel &&
          currentChannel.embedData &&
          isEmbedVisible
        "
        class="cut-edge__wrapper absolute z-30 transition-opacity-transform ease-linear duration-500"
        :class="[
          {
            invisible: !isEmbedVisible,
          },
        ]"
        :style="embedSize"
        ref="containerWrapper"
      >
        <CommonContainer
          @on-pin="(ev) => onPinHandler(ev, true)"
          @close-container="() => (isEmbedVisible = false)"
          @on-mouse-down="(ev) => onMouseDownHandler(ev, true)"
          :isPinActive="isPinBtnActive"
          :isMoveActive="isMoveBtnActive"
          :parentWrapper="containerWrapper"
        >
          <div class="flex-grow min-h-0 relative">
            <div class="absolute inset-0 bg-black overflow-hidden">
              <img
                v-if="showArt && image"
                :src="currentChannel.image.url"
                class="relative top-1/2 transform -translate-y-1/2 w-full"
              />
              <img
                v-else-if="showOverlay"
                alt="Embed's Custom Overlay"
                :src="currentChannel.overlay"
                class="relative top-1/2 transform -translate-y-1/2 w-full"
              />
            </div>
            <div
              class="relative w-full h-full transition-opacity ease-linear duration-500 delay-750 opacity-0 bg-black"
              :class="{ 'opacity-100': isEmbedVisible }"
            >
              <div class="absolute left-4 md:left-3 xl:left-6 top-2 w-2/3">
                <h5
                  class="cursor-default text-xxs text-white font-play truncate"
                >
                  {{ currentChannel.offlineDisplay.title }}
                </h5>
                <h6 class="cursor-default text-8 text-white font-play truncate">
                  {{ currentChannel.embedData.channel }}
                </h6>
              </div>
              <component
                v-if="currentChannel.embedData"
                ref="embed"
                :is="currentChannel.embedName"
                :embedData="currentChannel.embedData"
                :overlay="currentChannel.overlay"
                :image="currentChannel.image"
                :isShowTwitchEmbed
                class="w-full h-full"
                :width="'100%'"
                :height="'100%'"
              ></component>
            </div>
          </div>
          <a
            :href="currentChannel.link"
            class="cursor-default flex justify-between py-1 xl:pt-3 xl:pb-3 px-3 md:px-2 xl:px-4 bg-grey-900"
            :title="currentChannel.offlineDisplay.title"
          >
            <div class="cursor-default mr-2 overflow-hidden">
              <h5
                class="cursor-default text-xxs text-white font-play overflow-hidden text-ellipsis whitespace-nowrap"
              >
                {{ currentChannel.offlineDisplay.title }}
              </h5>
              <h6
                class="cursor-default text-8 text-grey font-play overflow-hidden text-ellipsis whitespace-nowrap"
              >
                {{ currentChannel.embedData.channel }}
              </h6>
            </div>
            <h6
              class="cursor-default text-8 text-grey font-play whitespace-nowrap"
            >
              {{ currentChannel.liveViewerCount }} viewers
            </h6>
          </a>
        </CommonContainer>
      </div>
    </Teleport>
  </div>
</template>
