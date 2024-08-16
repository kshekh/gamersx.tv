<script setup>
import { defineExpose, defineProps, onMounted, ref } from "vue";
import { useVideoStore } from "../stores/VideoStore";

const embed = ref(null);
const embedPlaying = ref(false);
const isBuffering = ref(false);

const videoStore = useVideoStore();

defineExpose({
  startPlayer,
  stopPlayer,
});

const { embedData, customBg, height, width } = defineProps({
  embedData: Object,
  customBg: Object,
  height: [Number, String],
  width: [Number, String],
});

function videoBuffered() {
  startPlayer();
}

function startPlayer() {
  if (!embedPlaying.value) {
    embed.play();
    videoStore.setVideoPlaying();
  }
}

function stopPlayer() {
  if (embedPlaying.value) {
    embed.pause();
    videoStore.setVideoNotPlaying();
  }
}

// function videoStore.setVideoPlaying()() {
//   embedPlaying.value = true;
// }
// function videoStore.setVideoNotPlaying()() {
//   embedPlaying.value = false;
// }

function embedTwitch() {
  embed.value = new Twitch.Embed(embedData.elementId, {
    width: width || 540,
    height: height || 300,
    channel: embedData.channel,
    video: embedData.video,
    layout: "video",
    autoplay: true,
    muted: true,
    controls: true,
    parent: window.location.hostname,
  });

  embed.value.addEventListener(Twitch.Player.PLAY, videoStore.setVideoPlaying);
  embed.value.addEventListener(
    Twitch.Player.PAUSE,
    videoStore.setVideoNotPlaying,
  );
  embed.value.addEventListener(
    Twitch.Player.ENDED,
    videoStore.setVideoNotPlaying,
  );
  embed.value.addEventListener(Twitch.Player.WAITING, () => {
    isBuffering.value = true;
  });
  embed.value.addEventListener(Twitch.Player.PLAYING, () => {
    isBuffering.value = false;
  });
  embed.value.addEventListener(Twitch.Player.OFFLINE, () => {
    embedPlaying.value = false;
    isBuffering.value = false;
  });
  embed.value.addEventListener(Twitch.Embed.VIDEO_READY, () => {
    isBuffering.value = false;
    videoBuffered;
  });

  videoStore.embeddedObject = embed.value;
}

onMounted(() => {
  embedTwitch();
});
</script>

<template>
  <div>
    <div v-if="isBuffering" :style="customBg"></div>
    <div
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
      class="h-full w-full"
      :id="embedData.elementId"
    ></div>
  </div>
</template>
