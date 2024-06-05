<template>
  <div @click="showTwitchEmbed = true" @mouseover="showTwitchEmbed = true">
    <img
      v-if="!showTwitchEmbed || !embed.videoTitle"
      :src="image"
      alt="Image missing"
      :style="{ height: height, width: width }"
    />
    <!-- <video
    v-if="!showTwitchEmbed || !embed.videoTitle"
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
    </video> -->

    <div :id="embedDataCopy.elementId"></div>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { computed, defineProps, ref, watch, watchSyncEffect } from 'vue'

import { useYoutubeEmbedStore } from "../stores/youtubeEmbedStore";

const props = defineProps([
  'embedData',
  'isShowTwitchEmbed',
  'height',
  'width',
]);

const { embed, embedPlaying, isBuffering } = storeToRefs(useYoutubeEmbedStore());
const image = ref("");
const isFirstTimeLoad = ref(true);
const showTwitchEmbed = ref(false);
const embedDataCopy = computed(() => {
  image.value = `https://img.youtube.com/vi/${this.embedData.video}/hqdefault.jpg`;
  return { ...this.embedData };
});
// const loadingVideo = computed(() => {
//   return loaders[Math.floor(Math.random() * loaders.length)];
// });
const store = useYoutubeEmbedStore();

store.$onAction(({ name }) => {
  if (name === 'startPlayer') {
    if (isFirstTimeLoad.value) {
      setTimeout(() => {
        if (!embedPlaying.value && showTwitchEmbed.value) {
          if (embed.value) {
            embed.value.playVideo();
            embed.value.unMute();
            embedPlaying.value = true;
            isFirstTimeLoad.value = false;
          }
        }
      }, 1000);
    } else {
      if (!embedPlaying.value) {
        if (embed.value) {
          embed.value.playVideo();
          embed.value.unMute();
          embedPlaying.value = true;
        }
      }
    }
  }
});

watch(showTwitchEmbed, (newVal) => {
  if (newVal === true) {
    store.createEmbed(embedDataCopy.value.video);
  }
});

watch(props.isShowTwitchEmbed, (newVal) => {
  if (newVal === true) {
    showTwitchEmbed.value = true
  }
});
</script>
