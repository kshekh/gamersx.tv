<script setup>
import {
  computed,
  defineAsyncComponent,
  onMounted,
  onUnmounted,
  ref,
} from "vue";
import { useContainerStore } from "../../stores/ContainerStore";
import { useVideoStore } from "../../stores/VideoStore";
import CommonContainer from "../CommonContainer/CommonContainer.vue";
import PlayButton from "../../helpers/PlayButton.vue";

// Props
const props = defineProps({
  title: {
    required: false,
  },
  info: {
    required: false,
  },
  customArt: {
    required: false,
  },
  channelName: {
    required: false,
  },
  showOnline: {
    required: false,
  },
  onlineDisplay: {
    required: false,
  },
  offlineDisplay: {
    required: false,
  },
  rowName: {
    required: false,
  },
  image: {
    required: false,
  },
  overlay: {
    required: false,
  },
  link: {
    required: false,
  },
  componentName: {
    required: false,
  },
  embedName: {
    required: false,
  },
  embedData: {
    required: false,
  },
  liveViewerCount: {
    required: false,
  },
  isGlowStyling: {
    required: false,
  },
  isCornerCut: {
    required: false,
  },
  broadcast: {
    required: false,
  },
});

console.log(props);

const containerStore = useContainerStore();
const videoStore = useVideoStore();

// Data
const cornerCutStyling = ref({
  outline: "",
  outlineBorder: "",
});
const embed = ref(null);
const glowStyling = ref({
  glow: "",
});
const isEmbedVisible = ref(false);
const isMobileDevice = ref(false);
const isOverlayVisible = ref(true);
const isTitleVisible = ref(false);
const isShowTwitchEmbed = ref(false);

// Computed
const embedContainerName = computed(() => {
  return defineAsyncComponent(() =>
    props.embedName === "TwitchEmbed"
      ? import("../../embeds/TwitchEmbed.vue")
      : import("../../embeds/YouTubeEmbed.vue"),
  );
});

const embedSize = computed(() => {
  let width = window.innerWidth > 1279 ? 400 : 355;
  let height = window.innerWidth > 1279 ? 350 : 311;

  return {
    width: width + "px",
    height: height + "px",
  };
});
const showArt = computed(() => {
  return props.onlineDisplay.showArt || props.offlineDisplay.showArt;
});

const showEmbed = computed(() => {
  return props.onlineDisplay.showEmbed || props.offlineDisplay.showEmbed;
});

const showOverlay = computed(() => {
  return props.onlineDisplay.showOverlay || props.offlineDisplay.showOverlay;
});

const getGlow = computed(() => {
  if (props.isGlowStyling) {
    return glowStyling.value.glow;
  } else {
    return "";
  }
});

const getOutline = computed(() => {
  if (props.isCornerCut) {
    return cornerCutStyling.value.outline;
  } else {
    return "";
  }
});

const getOutlineBorder = computed(() => {
  computeGlowStyling();
  return cornerCutStyling.value.outlineBorder;
});

const playBtnColor = computed(() => {
  return props.embedName === "TwitchEmbed" ? "twitch" : "youtube";
});

// Methods
function clickContainer(elementId) {
  const container = document.getElementById(elementId);
  container.click();
}

function computeGlowStyling() {
  if (
    props.isGlowStyling === "always_on" ||
    (props.isGlowStyling === "enabled_if_live" && props.showOnline) ||
    (props.isGlowStyling === "enabled_if_offline" && !props.showOnline)
  ) {
    if (props.embedName === "TwitchEmbed") {
      glowStyling.value.glow = "cut-edge__wrapper--twitch";
      cornerCutStyling.value.outlineBorder =
        "cut-edge__clipped--twitch border-purple";
    } else if (props.embedName === "YouTubeEmbed") {
      glowStyling.value.glow = "cut-edge__wrapper--youtube";
      cornerCutStyling.value.outlineBorder =
        "cut-edge__clipped--youtube border-red";
    }
  }

  if (
    props.isCornerCut === "always_on" ||
    (props.isCornerCut === "enabled_if_live" && props.showOnline) ||
    (props.isCornerCut === "enabled_if_offline" && !props.showOnline)
  ) {
    if (props.embedName === "TwitchEmbed") {
      cornerCutStyling.value.outline = "cut-edge__clipped--twitch";
    } else if (props.embedName === "YouTubeEmbed") {
      cornerCutStyling.value.outline = "cut-edge__clipped--youtube";
    }
  }
}

const handleClick = (embedData) => {
  if (!videoStore.activeEmbedIsEmpty) {
    videoStore.clearExistingEmbed();
  }

  containerStore.$reset();

  videoStore.resetStyles();
  videoStore.storeEmbed(embedData);
  videoStore.setStyles();

  startVideo.value = true;
  isEmbedVisible.value = true;

  containerStore.setContainerId(embedData.elementId);
};

function handleCloseContainer() {
  isEmbedVisible.value = false;
  containerStore.clearContainerId();
}

function playVideo() {
  setTimeout(() => {
    if (showOverlay.value || showArt.value) {
      isOverlayVisible.value = false;
      isEmbedVisible.value = true;
    }
  }, 0);

  if (embed.value && embed.value.startPlayer) {
    embed.value.startPlayer();
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

function scrollOut() {
  if (containerStore.isVisibleVideoContainer) {
    return;
  }
  if (showOverlay.value || showArt.value) {
    isOverlayVisible.value = true;
    isEmbedVisible.value = false;
  }
}

// Lifecycle Hooks
onMounted(() => {
  setIsMobileDevice();
  computeGlowStyling();
});
</script>

<template>
  <div
    class="cursor-default lex items-center w-18 h-24 md:w-24 md:h-32 xl:w-30 xl:h-40 shrink-0"
    ref="itemWrapper"
    v-if="!isMobileDevice"
  >
    <div
      @click="isShowTwitchEmbed = true"
      class="cut-edge__wrapper relative w-full h-full z-10"
      style="aspect-ratio: 3/4"
      :class="getGlow"
    >
      <div
        class="w-full h-full border-[3px] !border-[#7A4ECC]/40 rounded-[10px] bg-black shrink-0"
        :class="getOutline"
      >
        <!-- Show the embed with overlay if there's an embed -->
        <div
          v-if="showEmbed && props.embedData"
          class="w-full h-full overflow-hidden"
          @click="handleClick(embedData.elementId)"
        >
          <img
            v-if="showArt && props.image"
            :src="props.image.url"
            class="relative top-1/2 transform -translate-y-1/2 w-full"
            style="height: inherit"
          />
          <img
            v-else-if="showOverlay"
            alt="Embed's Custom Overlay"
            :src="overlay"
            class="relative top-1/2 transform -translate-y-1/2 w-full h-full object-cover"
          />
          <!--          <img-->
          <!--            src="/images/live-icon.gif"-->
          <!--            class="" style="position: absolute;top: 0px;width: 75px;right: 0;"-->
          <!--          />-->
          <play-button
            :videoType="playBtnColor"
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 pointer-events-none"
          />
        </div>

        <!-- If there's no embed, show that instead with a link first -->
        <div v-else-if="showArt && props.image" class="w-full h-full">
          <a :href="link" class="block w-full h-full overflow-hidden">
            <img
              :src="props.image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
          </a>
        </div>

        <!-- If there's only an overlay and isn't art, show that instead with a link -->
        <div v-else-if="showOverlay" class="w-full h-full">
          <a :href="props.link" class="block w-full h-full overflow-hidden">
            <img
              class="relative top-1/2 transform -translate-y-1/2 w-full"
              alt="Embed's Custom Overlay"
              :src="props.overlay"
            />
          </a>
        </div>
      </div>
    </div>

    <div
      v-if="showEmbed && props.embedData"
      class="cut-edge__wrapper absolute z-30 transition-opacity-transform ease-linear duration-500"
      :class="[
        getGlow,
        {
          invisible: !isEmbedVisible,
        },
      ]"
      ref="embedWrapper"
      :style="embedSize"
    >
      <CommonContainer
        @close-container="handleCloseContainer"
        :innerWrapperClassNames="getOutline"
      >
        <div class="flex-grow min-h-0 relative">
          <div class="absolute inset-0 bg-black overflow-hidden">
            <img
              v-if="showArt && props.image"
              :src="props.image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="props.overlay"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
          </div>
          <div
            class="relative w-full h-full transition-opacity ease-linear duration-500 delay-750 opacity-0 bg-black"
            :class="{ 'opacity-100': isEmbedVisible }"
          >
            <div class="absolute left-4 md:left-3 xl:left-6 top-2 w-2/3">
              <h5 class="cursor-default text-xxs text-white font-play truncate">
                {{ props.offlineDisplay.title }}
              </h5>
              <h6 class="cursor-default text-8 text-white font-play truncate">
                {{ props.embedData.channel }}
              </h6>
            </div>
            <component
              v-if="props.embedData"
              ref="embed"
              :is="props.embedName"
              :embedData="props.embedData"
              :overlay="props.overlay"
              :image="props.image"
              :isShowTwitchEmbed="isShowTwitchEmbed"
              class="w-full h-full"
              :width="'100%'"
              :height="'100%'"
            ></component>
          </div>
        </div>
        <a
          :href="props.link"
          class="cursor-default flex justify-between py-1 xl:pt-3 xl:pb-3 px-3 md:px-2 xl:px-4 bg-grey-900"
          :title="props.offlineDisplay.title"
        >
          <div class="cursor-default mr-2 overflow-hidden">
            <h5
              class="cursor-default text-xxs text-white font-play overflow-hidden text-ellipsis whitespace-nowrap"
            >
              {{ props.offlineDisplay.title }}
            </h5>
            <h6
              class="cursor-default text-8 text-grey font-play overflow-hidden text-ellipsis whitespace-nowrap"
            >
              {{ props.embedData.channel }}
            </h6>
          </div>
          <h6
            class="cursor-default text-8 text-grey font-play whitespace-nowrap"
          >
            {{ props.liveViewerCount }} viewers
          </h6>
        </a>
      </CommonContainer>
    </div>
  </div>

  <div
    class="flex items-center w-18 h-24 md:w-24 md:h-32 xl:w-30 xl:h-40 shrink-0"
    v-else
  >
    <div
      @click="isShowTwitchEmbed = true"
      v-show="!isEmbedVisible"
      class="cut-edge__wrapper h-full w-50 xs:pr-26 sm:pr-16 lg:pr-0 xl:pr-16 2xl:pr-0 xs:w-75 md:w-75 lg:w-80 xl:w-118 2xl:w-127"
      style="aspect-ratio: 3/4"
      :class="getGlow"
    >
      <div
        class="cut-edge__clipped cut-edge__clipped-top-right-md h-full bg-black overflow-hidden"
        :class="{ getOutline: true, 'numbered-mobile': isMobileDevice }"
      >
        <img
          v-if="showArt && props.image"
          :src="props.image.url"
          class="-translate-y-1/2 relative top-1/2 transform w-30p h-full object-cover"
        />

        <img
          v-else-if="showOverlay"
          alt="Embed's Custom Overlay"
          :src="props.overlay"
          class="relative top-1/2 transform -translate-y-1/2 w-full h-full object-cover"
        />
        <play-button
          v-if="showEmbed && props.embedData"
          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 h-12 md:h-16 xl:h-32 w-12 md:w-16 xl:w-32"
          svgClass="w-3 md:w-7 xl:w-12"
          wrapperClass="md:pl-1.5 xl:pl-3"
          :videoType="playBtnColor"
          @click.native="playVideo"
        />
      </div>
    </div>

    <!-- Show the embed with overlay if there's an embed -->
    <div v-if="showEmbed && props.embedData">
      <div
        class="cut-edge__wrapper flex-grow min-h-0 absolute inset-0 z-20 py-5 md:py-8 xl:py-12 px-4 md:px-18 xl:px-32 opacity-0 transition-opacity duration-300 ease-linear custom-embed-m"
        :class="[
          getOutlineBorder,
          {
            'opacity-100': isEmbedVisible,
            'pointer-events-none z-negative': !isEmbedVisible,
          },
        ]"
        style="
          top: 50%;
          left: 0;
          transform: translateY(-50%);
          z-index: 99;
          display: flex;
          align-items: center;
          width: auto !important;
        "
      >
        <div
          ref="embedWrapper"
          :class="{ 'relative w-full h-full main-parent': true }"
        >
          <component
            v-if="props.embedData"
            ref="embed"
            :is="embedContainerName"
            :embedData="props.embedData"
            :overlay="props.overlay"
            :image="props.image"
            :isShowTwitchEmbed="isShowTwitchEmbed"
            :isMobileDevice="isMobileDevice"
            class="h-full w-full border overflow-hidden bg-black"
            :class="{
              'border-purple': props.embedName === 'TwitchEmbed',
              'border-red': props.embedName === 'YouTubeEmbed',
            }"
            :width="'100%'"
            :height="'100%'"
          ></component>
        </div>
      </div>
    </div>
  </div>
</template>
