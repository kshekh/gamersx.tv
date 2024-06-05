<template>
  <div @mouseover="handleShowEmbed">
    <img
      v-if="image && isBuffering"
      :src="image.url"
      class="relative top-1/2 transform -translate-y-1/2 w-full"
      alt="Embed art"/>
    <video
      v-else-if="overlay && isBuffering"
      autoplay="autoplay"
      muted="muted"
      loop="loop"
      playsinline=""
      class="h-full md:w-full object-cover"
    >
      <source
        :src="loadingVideo"
        type="video/mp4"
      />
    </video>
    <iframe
      v-if="embed.type === 'twitch_clip'"
      :id="embed.elementId"
      ref="videoIframe"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
      :src="`${embed.url}&autoplay=true`"
      @load="handleIframeLoad"
      width="854"
      height="480"
      frameborder="0"
      :allowfullscreen="true"
      scrolling="no"
    ></iframe>
    <div
      v-else
      :id="embed.elementId"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
    ></div>
  </div>
</template>

<script setup>
import { computed, defineProps, ref, watch } from 'vue';
import { useTwitchEmbedStore } from '../stores/twitchEmbedStore';
import { storeToRefs } from 'pinia'

const props = defineProps({
  embedData: Object,
  image: Object,
  overlay: String,
  isShowTwitchEmbed: Boolean,
  isMobileDevice: Boolean,
  height: [Number, String],
  width: [Number, String],
  info: {},
  broadcast: {},
});

const loadingVideo = computed(() => {
  return loaders[Math.floor(Math.random() * loaders.length)];
});

const { embed, embedPlaying, isBuffering } = storeToRefs(useTwitchEmbedStore());
const channel = props.embedData.channel;
const elementId = props.embedData.elementId;
const height = props.height;
const parent = window.location.hostname;
const width = props.width;
const video = props.embedData.video;
const showTwitchEmbed = ref(false);
const store = useTwitchEmbedStore();
const loaders = ['/images/Sequence_01_final.mp4'];

let element = document.getElementById(embedData.elementId)
if (element.children.length === 0) {
  store.createEmbed(channel, elementId, height, parent, video, width);

  embed.value.addEventListener(Twitch.Player.PLAY, store.setIsPlaying);
  embed.value.addEventListener(Twitch.Player.PAUSE, store.setIsNotPlaying);
  embed.value.addEventListener(Twitch.Player.ENDED, store.setIsNotPlaying);
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
} else {
  isBuffering.value = false;
  store.startPlayer();
}

function handlePlayerStateChanged(event) {
  const { video_id, play, play_reason } = event.detail;
  if (video_id === this.embedDataCopy.elementId) {
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
}

function handleShowEmbed() {
  showTwitchEmbed.value = true;
}
function handleIframeLoad(e) {
  isBuffering.value = false;
}

watch(showTwitchEmbed, (newVal) => {
  if (newVal === true) {
    store.createEmbed(channel, elementId, height, parent, video, width);
  }
});
</script>
