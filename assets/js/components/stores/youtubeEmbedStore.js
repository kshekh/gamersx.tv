import { defineStore } from "pinia";
import { computed, ref } from 'vue'

export const useYoutubeEmbedStore = defineStore("youtubeEmbedStore", () => {
  const embed = ref({});
  const embedData = ref({});
  const loaders = ref(["/images/Sequence_01_final.mp4"]);
  const embedPlaying = ref(false);
  const isBuffering = ref(true);
  const isMobileDevice = computed(() => {
    return navigator.userAgent.toLowerCase().match(/mobile/i)
  });
  const isPlaying = computed(() => embedPlaying.value);
  function createEmbed(video) {
    embed.value = new YT.Player(this.embedDataCopy.elementId, {
      width: this.width || 540,
      height: this.height || 300,
      videoId: video,
      autoplay: false,
      playerVars: {
        modestbranding: true,
        rel: 0,
      },
      events: {
        onStateChange: this.playerStateChanged,
      },
    })
  }

  function startPlayer() {
    this.embed.play();
    this.embed.setMuted(false);
    this.embedPlaying = true;
  }
  function stopPlayer() {
    if (this.embedPlaying) {
      this.embed.pause();
      this.embedPlaying = false;
    }
  }

  return { embed, embedData, loaders, embedPlaying, isBuffering, isMobileDevice, createEmbed, startPlayer, stopPlayer };
});