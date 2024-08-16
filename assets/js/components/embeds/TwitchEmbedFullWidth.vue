<script setup>
import { defineExpose, defineProps, onMounted, ref } from "vue";

const embed = ref(null);
const embedPlaying = ref(false);
const isBuffering = ref(false);

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
    setIsPlaying();
  }
}

function stopPlayer() {
  if (embedPlaying.value) {
    embed.pause();
    setIsNotPlaying();
  }
}

function setIsPlaying() {
  embedPlaying.value = true;
}
function setIsNotPlaying() {
  embedPlaying.value = false;
}

function embedTwitch() {
  embed.value = new Twitch.Embed(this.embedData.elementId, {
    width: this.width || 540,
    height: this.height || 300,
    channel: this.embedData.channel,
    video: this.embedData.video,
    layout: "video",
    autoplay: true,
    muted: true,
    controls: true,
    parent: window.location.hostname,
  });

  embed.value.addEventListener(Twitch.Player.PLAY, this.setIsPlaying);
  embed.value.addEventListener(Twitch.Player.PAUSE, this.setIsNotPlaying);
  embed.value.addEventListener(Twitch.Player.ENDED, this.setIsNotPlaying);
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
    videoBuffered();
  });
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
