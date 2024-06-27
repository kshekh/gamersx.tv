import { defineStore } from "pinia";
import { computed, ref } from "@vue/compat";

export const useTwitchEmbedStore = defineStore('twitchEmbedStore', () => {
  const embed = ref({});
  const embedData = ref({});
  const loaders = ref(["/images/Sequence_01_final.mp4"]);
  const embedPlaying = ref(false);
  const isPlaying = computed(() => embedPlaying.value);
  const isBuffering = ref(true);
  const isMobileDevice = computed(() => {
    return navigator.userAgent.toLowerCase().match(/mobile/i)
  });

  function createEmbed(elementId, height, width, channel, vodId, parent) {
    const embed = new Twitch.Embed(elementId, {
      width: width || 540,
      height: height || 300,
      channel: channel,
      video: vodId,
      layout: "video",
      autoplay: true,
      muted: true,
      controls: true,
      parent: parent,
    });

    // embed.value.addEventListener(Twitch.Player.PLAY, () => {
    //   this.embedPlaying.value = true;
    // });
    // embed.value.addEventListener(Twitch.Player.PAUSE, () => {
    //   this.embedPlaying.value = false;
    // });
    // embed.value.addEventListener(Twitch.Player.ENDED, () => {
    //   this.embedPlaying.value = false;
    // });
    // embed.value.addEventListener(Twitch.Player.WAITING, () => {
    //   this.isBuffering.value = true;
    // });
    // embed.value.addEventListener(Twitch.Player.PLAYING, () => {
    //   this.isBuffering.value = false;
    // });
    // embed.value.addEventListener(Twitch.Player.OFFLINE, () => {
    //   this.embedPlaying.value = false;
    //   this.isBuffering.value = false;
    // });
  }

  function startPlayer() {
    if (!isPlaying) {
      embed.play();
      setIsNotPlaying();
    }
  }
  function stopPlayer() {
    if (isPlaying) {
      embed.pause();
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

  function checkIfMobileDevice() {
    return navigator.userAgent.toLowerCase().match(/mobile/i);
  }

  return {
    embed,
    embedPlaying,
    isBuffering,
    isPlaying,
    createEmbed,
    setIsPlaying,
    setIsNotPlaying,
    startPlayer,
    stopPlayer,
    videoBuffered,
  };
});