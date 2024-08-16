<script setup>
import { ref, computed, defineExpose, onMounted, onUnmounted } from "vue";
import { useVideoStore } from "../stores/VideoStore";

const {
  embedData,
  image,
  overlay,
  isMobileDevice,
  height,
  width,
  info,
  broadcast,
} = defineProps({
  embedData: Object,
  image: Object,
  overlay: String,
  isMobileDevice: Boolean,
  height: [Number, String],
  width: [Number, String],
  info: Object,
  broadcast: Object,
});

const embed = ref(null);
const embedPlaying = ref(false);
const showTwitchEmbed = ref(false);
const isBuffering = ref(true);
const loaders = ["/images/Sequence_01_final.mp4"];

const videoStore = useVideoStore();

defineExpose({
  startPlayer,
  stopPlayer,
});

const embedDataCopy = computed(() => ({ ...embedData }));
const loadingVideo = computed(
  () => loaders[Math.floor(Math.random() * loaders.length)],
);

const handlePlayerStateChanged = (event) => {
  const { video_id, play, play_reason } = event.detail;
  if (video_id === embedDataCopy.value.elementId) {
    if (play && play_reason === "auto") {
      embedPlaying.value = true;
      isBuffering.value = false;
    } else if (!play && play_reason === "buffering") {
      embedPlaying.value = false;
      isBuffering.value = true;
    } else {
      embedPlaying.value = false;
      isBuffering.value = false;
    }
  }
};

const handleIframeLoad = (e) => {
  isBuffering.value = false;
};

function embedTwitch() {
  if (!embed.value) {
    console.log("i will embed");

    // Ensure embed is created safely without cross-origin access
    embed.value = new Twitch.Embed(embedDataCopy.value.elementId, {
      width: width || 540,
      height: height || 300,
      channel: embedDataCopy.value.channel,
      video: embedDataCopy.value.video,
      layout: "video",
      autoplay: true,
      muted: false,
      controls: false,
      parent: window.location.hostname,
    });

    // Listen to events via Twitch API
    embed.value.addEventListener(Twitch.Player.PLAY, () => {
      videoStore.setVideoPlaying();
    });
    embed.value.addEventListener(Twitch.Player.PAUSE, () => {
      videoStore.setVideoNotPlaying();
    });
    embed.value.addEventListener(Twitch.Player.ENDED, () => {
      videoStore.setVideoNotPlaying();
    });
    embed.value.addEventListener(Twitch.Player.WAITING, () => {
      isBuffering.value = true;
    });
    embed.value.addEventListener(Twitch.Player.PLAYING, () => {
      isBuffering.value = false;
    });
    embed.value.addEventListener(Twitch.Player.OFFLINE, () => {
      videoStore.setVideoNotPlaying();
      isBuffering.value = false;
    });

    console.log("the value of the embed is ", embed.value);
  } else {
    isBuffering.value = false;
    startPlayer();
  }
}

// watch(
//   [showTwitchEmbed, isShowTwitchEmbed],
//   (newVal) => {
//     if (newVal) {
//       embedTwitch();
//     }
//   },
//   { immediate: true },
// );

function startPlayer() {
  if (!embedPlaying.value && (showTwitchEmbed.value || isShowTwitchEmbed)) {
    embed.value?.play();
    embed.value?.setMuted(false);
    embedPlaying.value = true;
  }
}

function stopPlayer() {
  if (embedPlaying.value) {
    embed.value.pause();
    embedPlaying.value = false;
  }
}

onMounted(() => {
  embedTwitch();
  // window.addEventListener("message", handlePlayerStateChanged);
});

onUnmounted(() => {
  // window.removeEventListener("message", handlePlayerStateChanged);
});
</script>

<template>
  <div>
    <img
      v-if="!!(image && isBuffering)"
      :src="image.url"
      class="relative top-1/2 transform -translate-y-1/2 w-full"
    />
    <video
      v-else-if="!!(overlay && isBuffering)"
      autoplay
      muted
      loop
      playsinline
      class="h-full md:w-full object-cover"
    >
      <source :src="loadingVideo" type="video/mp4" />
    </video>
    <iframe
      v-if="embedDataCopy.type === 'twitch_clip'"
      :id="embedDataCopy.elementId"
      ref="videoIframe"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="{ display: isBuffering ? 'none' : 'block' }"
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
      :style="{ display: isBuffering ? 'none' : 'block' }"
    ></div>
  </div>
</template>
