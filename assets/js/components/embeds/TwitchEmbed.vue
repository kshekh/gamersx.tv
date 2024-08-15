<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useTwitchEmbed } from "../composables/twitchEmbed";
import { useVideoStore } from "../stores/VideoStore";

const props = defineProps({
  image: Object,
  overlay: String,
  isShowTwitchEmbed: Boolean,
  isMobileDevice: Boolean,
  height: [Number, String],
  width: [Number, String],
  info: {},
  broadcast: {},
});

const videoIframe = ref(null); // Ref for the iframe

const {
  isBuffering,
  showTwitchEmbed,
  embedTwitch,
  handleIframeLoad,
  loadingVideo,
} = useTwitchEmbed(
  ref(useVideoStore().embedData),
  false,
  props.height,
  props.width,
); // Use your composable

const embedDataCopy = computed(() => {
  return { ...props.embedData };
});

watch(showTwitchEmbed, (newVal) => {
  if (newVal === true) {
    embedTwitch();
  }
});

watch(
  () => props.isShowTwitchEmbed,
  (newVal) => {
    if (newVal === true) {
      embedTwitch();
    }
  },
);

onMounted(() => embedTwitch());
</script>

<template>
  <div @mouseover="showTwitchEmbed = true">
    <img
      v-if="!!(image && isBuffering)"
      :src="image['url']"
      class="relative top-1/2 transform -translate-y-1/2 w-full"
    />
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
      v-if="embedDataCopy.type === 'twitch_clip'"
      :id="embedDataCopy.elementId"
      ref="videoIframe"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
      :src="`${embedDataCopy.url}&autoplay=true`"
      @load="handleIframeLoad"
      width="854"
      height="480"
      frameborder="0"
      allowfullscreen="true"
      scrolling="no"
    ></iframe>
    <div
      v-else
      :id="embedDataCopy.elementId"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
    ></div>
  </div>
</template>
