<script setup>
import { computed, ref, watch, defineProps } from "vue";
import TwitchEmbed from "../../embeds/TwitchEmbedFullWidth.vue";
import YouTubeEmbed from "../../embeds/YouTubeFullWidth.vue";

// Props
const props = defineProps({
  title: String,
  info: String,
  customArt: String,
  channelName: String,
  showOnline: String,
  onlineDisplay: Object,
  offlineDisplay: Object,
  rowName: String,
  image: String,
  overlay: String,
  link: String,
  componentName: String,
  embedName: String,
  embedData: Object,
  liveViewerCount: String,
  isAllowPlaying: Boolean,
  isRowFirst: Boolean,
  isFirstVideoLoaded: Boolean,
  isMouseStopped: Boolean,
  description: String,
  customBg: Object,
});

// Data
const embed = ref(null);
const isEmbedVisible = ref(true);
const isHideButtonClicked = ref(false);
const isVideoBuffered = ref(false);
const isVideoPlaying = ref(false);

// Computed Properties
const bgColor = computed(() =>
  props.embedName === "TwitchEmbed"
    ? "bg-purple/30 hover:bg-purple"
    : "bg-red/30 hover:bg-red",
);

const decreaseInfoBoxSize = computed(
  () => isVideoBuffered.value && (isVideoPlaying.value || isEmbedVisible.value),
);

const isInfoBoxHidden = computed(() => isVideoPlaying.value);

const showArt = computed(
  () =>
    (props.showOnline && props.onlineDisplay.showArt) ||
    (!props.showOnline && props.offlineDisplay.showArt),
);

const showEmbed = computed(
  () =>
    (props.showOnline && props.onlineDisplay.showEmbed) ||
    (!props.showOnline && props.offlineDisplay.showEmbed),
);

const showOverlay = computed(
  () =>
    props.overlay &&
    ((props.showOnline && props.onlineDisplay.showOverlay) ||
      (!props.showOnline && props.offlineDisplay.showOverlay)),
);

// Methods
function handleHideButtonClick() {
  isHideButtonClicked.value = true;
  playVideo();
}

function hideVideo() {
  isHideButtonClicked.value = false;
  isEmbedVisible.value = false;
  if (embed.value) {
    embed.value.stopPlayer();
  }
}

function playVideo() {
  embed.value.startPlayer();
}

function stopVideo() {
  embed.value.stopPlayer();
}

// Watchers
watch(
  () => props.isAllowPlaying,
  (status) => {
    if (status) {
      playVideo();
    } else {
      stopVideo();
      isHideButtonClicked.value = false;
    }
  },
);
</script>

<template>
  <div
    class="cursor-default relative z-10 flex flex-col opacity-1 transform rounded-md transition-all duration-700 backdrop-filter backdrop-blur-xs shadow-smooth px-7 -mx-7"
    :class="[
      decreaseInfoBoxSize
        ? 'md:max-w-1/4 md:min-w-180'
        : 'max-w-1/2 md:max-w-1/3 md:min-w-240',
      {
        'opacity-0 translate-y-3': isInfoBoxHidden,
        'pointer-events-none': isHideButtonClicked,
      },
    ]"
  >
    <div
      class="overflow-hidden transition-all duration-300"
      :class="[
        decreaseInfoBoxSize
          ? 'md:mb-1 h-7 md:h-10 xl:h-19'
          : 'mb-1 md:mb-2 h-14 md:h-26 xl:h-52',
      ]"
    >
      <img
        v-if="showArt && image && !overlay"
        :src="image.url"
        class="max-h-20 md:max-h-28 xl:max-h-52"
      />

      <img
        v-else-if="showOverlay"
        alt="Embed's Custom Overlay"
        :src="overlay"
        class="max-h-20 md:max-h-28 xl:max-h-52"
      />
      <img
        v-else-if="overlay && offlineDisplay.showEmbed"
        alt="Embed's Custom Overlay"
        :src="overlay"
        class="max-h-20 md:max-h-28 xl:max-h-52"
      />

      <div v-if="isBuffering" :style="customBg"></div>
      <video
        v-else-if="!!(overlay && isBuffering)"
        autoplay="autoplay"
        muted="muted"
        loop="loop"
        playsinline=""
        class="h-full md:w-full object-cover"
      >
        <source :src="loadingVideo" type="video/mp4" />
      </video>
      <iframe
        v-if="embedDataCopy.type === 'twitch_clip' && !isBuffering"
        :id="embedDataCopy.elementId"
        ref="videoIframe"
        class="h-full w-full m-w-[355px] m-h-[311px]"
        :src="`${embedDataCopy.url}&autoplay=true`"
        @load="handleIframeLoad"
        width="854"
        height="480"
        frameborder="0"
        allowfullscreen="true"
        scrolling="no"
      ></iframe>
      <div
        v-else-if="!isBuffering"
        :id="embedDataCopy.elementId"
        class="h-full w-full m-w-[355px] m-h-[311px]"
      ></div>
    </div>

    <p
      class="cursor-default text-white transition-all duration-300"
      :class="[
        decreaseInfoBoxSize
          ? 'cursor-default text-8 md:text-xs xl:text-sm mb-1 xl:mb-2'
          : 'cursor-default text-xs md:text-sm xl:text-lg mb-2 xl:mb-4',
      ]"
    >
      {{ description }}
    </p>

    <div
      class="mt-1 md:mt-auto transition-all duration-300 flex items-center"
      :class="[
        decreaseInfoBoxSize
          ? 'space-x-1 md:space-x-2 xl:space-x-3'
          : 'space-x-2 md:space-x-3 xl:space-x-4',
      ]"
    >
      <button
        @click.stop="handleHideButtonClick"
        class="text-white p-1 transition-all duration-300 bg-opacity-30 hover:bg-opacity-100"
        :class="[
          bgColor,
          decreaseInfoBoxSize
            ? 'text-8 md:text-xs xl:text-sm md:px-1 md:py-1 xl:py-2 xl:px-4 min-w-40 md:min-w-50 xl:min-w-75'
            : 'text-xs md:text-sm xl:text-lg min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6',
        ]"
      >
        Hide
      </button>

      <a
        :href="link"
        class="text-white p-1 transition-all duration-300 bg-opacity-30 text-center hover:bg-opacity-100"
        :class="[
          bgColor,
          decreaseInfoBoxSize
            ? 'text-8 md:text-xs xl:text-sm md:px-1 md:py-1 xl:py-2 xl:px-4 min-w-50 md:min-w-90 xl:min-w-75'
            : 'text-xs md:text-sm xl:text-lg min-w-50 md:min-w-75 xl:min-w-130 md:px-3 md:py-2 xl:py-3 xl:px-6',
        ]"
        target="_blank"
      >
        More info
      </a>

      <svg
        v-if="embedName === 'YouTubeEmbed'"
        class="w-3 md:w-6 xl:w-7 shrink-0"
        viewBox="0 0 44 32"
      ></svg>

      <svg
        v-if="embedName === 'TwitchEmbed'"
        class="w-3 md:w-6 xl:w-7 shrink-0"
        viewBox="0 0 8 8"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      ></svg>
    </div>
  </div>

  <div v-if="showEmbed && embedData">
    <div v-show="isAllowPlaying">
      <component
        v-if="isAllowPlaying"
        ref="embed"
        class="flex-grow min-h-0 absolute inset-0 full-width-embed-first-row"
        :is="embedName"
        :embedData="embedData"
        :isRowFirst="isRowFirst"
        :customBg="customBg"
        width="100%"
        height="100%"
        @video-buffered="videoBuffered"
        @set-is-playing="updateIsPlaying"
      ></component>
    </div>
  </div>
</template>
