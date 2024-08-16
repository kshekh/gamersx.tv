<script setup>
import {
  ref,
  computed,
  defineAsyncComponent,
  onMounted,
  watch,
  reactive,
} from "vue";
import { useContainerStore } from "../../stores/containerStore";
import { useVideoStore } from "../../stores/VideoStore";
import CommonContainer from "../CommonContainer/CommonContainer.vue";
import PlayButton from "../../helpers/PlayButton.vue";

const containerStore = useContainerStore();
const videoStore = useVideoStore();

const containerWrapper = ref(null);
const embed = ref(null);
const embedWrapper = ref(null);
const glowStyling = reactive({ glow: "", cornerCut: "" });
const isEmbedVisible = ref(false);
const isMobileDevice = ref(false);
const isShowTwitchEmbed = ref(false);
const startVideo = ref(false);

const {
  channel,
  title,
  channelName,
  showOnline,
  onlineDisplay,
  offlineDisplay,
  rowName,
  image,
  overlay,
  link,
  componentName,
  embedName,
  embedData,
  liveViewerCount,
  isGlowStyling,
  isCornerCut,
} = defineProps({
  channel: Object,
  title: String,
  channelName: String,
  showOnline: Boolean,
  onlineDisplay: Object,
  offlineDisplay: Object,
  rowName: String,
  image: Object,
  overlay: String,
  link: String,
  componentName: String,
  embedName: String,
  embedData: Object,
  liveViewerCount: Number,
  isGlowStyling: String,
  isCornerCut: String,
});

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

const computeGlowStyling = () => {
  glowStyling.glow = "";
  glowStyling.cornerCut = "";

  const conditions = [
    isGlowStyling === "always_on",
    isGlowStyling === "enabled_if_live" && showOnline,
    isGlowStyling === "enabled_if_offline" && !showOnline,
  ];

  if (conditions.some(Boolean)) {
    glowStyling.glow = `cut-edge__wrapper--${embedName}`;
    glowStyling.cornerCut = `cut-edge__clipped--${embedName}`;
  }
};

const setIsMobileDevice = () => {
  isMobileDevice.value = navigator.userAgent.toLowerCase().includes("mobile");
};

const playVideo = () => {
  setTimeout(() => {
    if (showOverlay.value || showArt.value) {
      isOverlayVisible.value = false;
      isEmbedVisible.value = true;
    }
  }, 0);

  if (embed.value && embed.value.startPlayer) {
    embed.value.startPlayer();
  }
};

const scrollOut = () => {
  if (containerStore.isVisibleVideoContainer) return;
  if (showOverlay.value || showArt.value) {
    isOverlayVisible.value = true;
    isEmbedVisible.value = false;
  }
};

const embedContainerName = computed(() => {
  return defineAsyncComponent(() =>
    embedName === "TwitchEmbed"
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
const getOutline = computed(() => [glowStyling.cornerCut]);
const getGlow = computed(() => glowStyling.glow);
const playBtnColor = computed(() => {
  return embedName === "TwitchEmbed" ? "twitch" : "youtube";
});
const showArt = computed(() => {
  return (
    (showOnline && onlineDisplay.showArt) ||
    (!showOnline && offlineDisplay.showArt)
  );
});
const showEmbed = computed(() => {
  return (
    (showOnline && onlineDisplay.showEmbed) ||
    (!showOnline && offlineDisplay.showEmbed)
  );
});
const showOverlay = computed(() => {
  return (
    overlay &&
    ((showOnline && onlineDisplay.showOverlay) ||
      (!showOnline && offlineDisplay.showOverlay))
  );
});

function handleCloseContainer() {
  isEmbedVisible.value = false;
  containerStore.clearContainerId();
}

onMounted(() => {
  setIsMobileDevice();
  computeGlowStyling();
});
</script>

<template>
  <div
    class="cursor-default w-full h-full shrink-0"
    ref="itemWrapper"
    v-if="!isMobileDevice"
  >
    <div class="cut-edge__wrapper w-full h-full" :class="getGlow">
      <div
        @click="isShowTwitchEmbed = true"
        class="w-full h-full border-[3px] rounded-[10px] overflow-hidden !border-[#7A4ECC]/40 cut-edge__clipped-top-left-sm bg-black"
        :class="getOutline"
      >
        <!-- Show the embed with overlay if there's an embed -->
        <div
          v-if="showEmbed && embedData"
          class="w-full h-full relative overflow-hidden"
          @click="handleClick(embedData)"
        >
          <img
            v-if="showArt && image"
            :src="image.url"
            class="relative top-1/2 transform -translate-y-1/2 w-full object-fit"
          />
          <img
            v-else-if="showOverlay"
            alt="Embed's Custom Overlay"
            :src="overlay"
            class="relative top-1/2 transform -translate-y-1/2 w-full object-cover"
          />
          <!--          <img-->
          <!--            v-if="showEmbed && embedData"-->
          <!--            src="/images/live-icon.gif"-->
          <!--            class="" style="position: Layout-sc-1xcs6mc-0 top-bar--pointer-enabledLayout-sc-1xcs6mc-0 top-bar--pointer-enabled;top: 10px;width: 75px;right: 0;"-->
          <!--          />-->
          <PlayButton
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 pointer-events-none"
            :videoType="playBtnColor"
          />
        </div>

        <!-- If there's no embed, show that instead with a link first -->
        <div v-else-if="showArt && image" class="w-full h-full">
          <a :href="link" class="block w-full h-full overflow-hidden">
            <img
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
          </a>
        </div>

        <!-- If there's only an overlay and isn't art, show that instead with a link -->
        <div v-else-if="showOverlay" class="w-full h-full">
          <a :href="link" class="block w-full h-full overflow-hidden">
            <img
              class="relative top-1/2 transform -translate-y-1/2 w-full"
              alt="Embed's Custom Overlay"
              :src="overlay"
            />
          </a>
        </div>
      </div>
    </div>

    <div
      v-if="
        showEmbed &&
        embedData &&
        isEmbedVisible &&
        containerStore.containerId === embedData.elementId
      "
      ref="containerWrapper"
      :style="embedSize"
      :class="[
        'cut-edge__wrapper',
        'absolute',
        'z-30',
        'transition-opacity-transform',
        'ease-linear',
        'duration-500',
        getGlow,
        {
          invisible: !isEmbedVisible,
        },
      ]"
    >
      <CommonContainer
        @close-container="handleCloseContainer"
        :innerWrapperClassNames="getOutline"
      >
        <div class="flex-grow min-h-0 relative">
          <div class="absolute inset-0 bg-black overflow-hidden">
            <img
              v-if="showArt && image"
              :src="image.url"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
            <img
              v-else-if="showOverlay"
              alt="Embed's Custom Overlay"
              :src="overlay"
              class="relative top-1/2 transform -translate-y-1/2 w-full"
            />
          </div>
          <div
            class="relative w-full h-full transition-opacity ease-linear duration-500 delay-750 opacity-0 bg-black"
            :class="{ 'opacity-100': isEmbedVisible }"
          >
            <div class="absolute left-4 md:left-3 xl:left-6 top-2 w-2/3">
              <h5 class="cursor-default text-xxs text-white font-play truncate">
                {{ offlineDisplay.title }}
              </h5>
              <h6 class="cursor-default text-8 text-white font-play truncate">
                {{ embedData.channel }}
              </h6>
            </div>
            <component
              v-if="embedData"
              ref="embed"
              :isShowTwitchEmbed="isShowTwitchEmbed"
              :is="embedContainerName"
              :embedData="embedData"
              :overlay="overlay"
              :image="image"
              class="w-full h-full"
              :width="'100%'"
              :height="'100%'"
            ></component>
          </div>
        </div>
      </CommonContainer>
    </div>
  </div>

  <div class="w-full h-full shrink-0" v-else>
    <div
      @click="isShowTwitchEmbed = true"
      v-show="!isEmbedVisible"
      class="h-full w-full xs:pr-26 sm:pr-16 lg:pr-0 xl:pr-16 2xl:pr-0 xs:w-75 md:w-75 lg:w-80 xl:w-118 2xl:w-127"
      style="aspect-ratio: 3/4"
      :class="getGlow"
    >
      <div
        class="cut-edge__clipped cut-edge__clipped-top-right-md h-full bg-black overflow-hidden"
        :class="getOutline"
      >
        <img
          v-if="showArt && image"
          :src="image.url"
          class="-translate-y-1/2 relative top-1/2 transform h-full object-cover"
        />

        <img
          v-else-if="showOverlay"
          alt="Embed's Custom Overlay"
          :src="overlay"
          class="relative top-1/2 transform -translate-y-1/2 w-full h-full object-cover"
        />
        <PlayButton
          v-if="showEmbed && embedData"
          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 h-12 md:h-16 xl:h-32 w-12 md:w-16 xl:w-32"
          svgClass="w-3 md:w-7 xl:w-12"
          wrapperClass="md:pl-1.5 xl:pl-3"
          :videoType="playBtnColor"
          @click.native="playVideo"
        />
      </div>
    </div>

    <!-- Show the embed with overlay if there's an embed -->
    <div v-if="showEmbed && embedData">
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
            v-if="embedData"
            ref="embed"
            :is="embedName"
            :embedData="embedData"
            :overlay="overlay"
            :image="image"
            :isShowTwitchEmbed="isShowTwitchEmbed"
            :isMobileDevice="isMobileDevice"
            class="h-full w-full border overflow-hidden bg-black"
            :class="{
              'border-purple': embedName === 'TwitchEmbed',
              'border-red': embedName === 'YouTubeEmbed',
            }"
            :width="'100%'"
            :height="'100%'"
          ></component>
        </div>
      </div>
    </div>
  </div>
</template>
