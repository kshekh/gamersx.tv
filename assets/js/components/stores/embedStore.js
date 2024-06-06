import { defineStore } from 'pinia';
import { computed, ref } from 'vue'

export const useEmbedStore = defineStore('embed', () => {
  const embed = ref({});
  const embedPlaying = ref(false);
  const isBuffering = ref(false);
  const isPlaying = computed(() => embedPlaying.value);

  function createTwitchEmbed(channel, elementId, height, parent, video, width) {
    embed.value = new Twitch.Embed(elementId, {
      width: width || 540,
      height: height || 300,
      channel: channel,
      video: video,
      layout: "video",
      autoplay: true,
      muted: true,
      controls: true,
      parent: parent,
    });
  }

  function createYouTubeEmbed(video) {
    embed.value = new YT.Player(this.embedDataCopy.elementId, {
      width: width || 540,
      height: height || 300,
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

  function createEmbed(channel, elementId, height, parent, video, width, embedType) {
    if (embedType === 'twitch') {
      createTwitchEmbed(channel, elementId, height, parent, video, width);
    } else if (embedType === 'youtube') {
      createYouTubeEmbed(video, height, width);
    }
  }

  function startPlayer() {
    if (!isPlaying) {
      embed.value.play();
      setIsNotPlaying();
    }
  }
  function stopPlayer() {
    if (isPlaying) {
      embed.value.pause();
      setIsPlaying();
    }
  }

  function setIsPlaying() {
    embedPlaying.value = true;
  }
  function setIsNotPlaying() {
    embedPlaying.value = false;
  }

  function videoBuffered() {
    startPlayer();
  }

  // YouTube
  function playerStateChanged(event) {
    if (event.data === YT.PlayerState.PLAYING) {
      setIsPlaying();
    } else {
      setIsNotPlaying();
    }
  }

  return {
    embed,
    embedPlaying,
    isBuffering,
    isPlaying,
    createEmbed,
    createYouTubeEmbed,
    setIsPlaying,
    setIsNotPlaying,
    startPlayer,
    stopPlayer,
    videoBuffered,
  };
});