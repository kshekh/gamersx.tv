<script setup>
import {
  ref,
  watch,
  computed,
  defineExpose,
  onMounted,
  onUnmounted,
} from "vue";

const props = defineProps({
  embedData: Object,
  image: Object,
  overlay: String,
  isMobileDevice: Boolean,
  height: [Number, String],
  width: [Number, String],
  info: {},
  broadcast: {},
});

const embed = ref(null);
const embedPlaying = ref(false);
const showTwitchEmbed = ref(false);
const isBuffering = ref(true);
const loaders = ["/images/Sequence_01_final.mp4"];

defineExpose({
  startPlayer,
  stopPlayer,
});

const embedDataCopy = computed(() => ({ ...props.embedData }));
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

watch(
  [showTwitchEmbed, props.isShowTwitchEmbed],
  (newVal) => {
    if (newVal) {
      embedTwitch();
    }
  },
  { immediate: true },
);

const embedTwitch = async () => {
  if (!embed.value) {
    const element = document.getElementById(embedDataCopy.value.elementId);
    if (element.children.length === 0) {
      embed.value = new Twitch.Embed(embedDataCopy.value.elementId, {
        width: props.width || 540,
        height: props.height || 300,
        channel: embedDataCopy.value.channel,
        video: embedDataCopy.value.video,
        layout: "video",
        autoplay: true,
        muted: false,
        controls: false,
        parent: window.location.hostname,
      });

      embed.value.addEventListener(Twitch.Player.PLAY, () => {
        console.log("PLAY event triggered");
        embedPlaying.value = true;
      });
      embed.value.addEventListener(Twitch.Player.PAUSE, () => {
        console.log("PAUSE event triggered");
        embedPlaying.value = false;
      });
      embed.value.addEventListener(Twitch.Player.ENDED, () => {
        console.log("ENDED event triggered");
        embedPlaying.value = false;
      });
      embed.value.addEventListener(Twitch.Player.WAITING, () => {
        console.log("WAITING event triggered");
        isBuffering.value = true;
      });
      embed.value.addEventListener(Twitch.Player.PLAYING, () => {
        console.log("PLAYING event triggered");
        isBuffering.value = false;
        console.log("i set is buffering to ", isBuffering.value);
      });
      embed.value.addEventListener(Twitch.Player.OFFLINE, () => {
        console.log("OFFLINE event triggered");
        embedPlaying.value = false;
        isBuffering.value = false;
      });
      console.log("is it still buffering? ", isBuffering.value);
    } else {
      isBuffering.value = false;
      startPlayer();
    }
  } else {
    startPlayer();
  }
};

function startPlayer() {
  if (
    !embedPlaying.value &&
    (showTwitchEmbed.value || props.isShowTwitchEmbed)
  ) {
    console.log("details regarding the embed", embed.value);
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
  window.addEventListener("message", handlePlayerStateChanged);
});

onUnmounted(() => {
  window.removeEventListener("message", handlePlayerStateChanged);
});
</script>

<template>
  <div @mouseover="showTwitchEmbed">
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
