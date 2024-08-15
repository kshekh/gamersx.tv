import { ref, onMounted, watch, computed, shallowRef } from "vue";

export function useTwitchEmbed(
  embedData,
  isFullWidth,
  height = 300,
  width = 540,
) {
  const embed = shallowRef(null);
  const embedPlaying = ref(false);
  const isBuffering = ref(true);
  const showTwitchEmbed = ref(false);

  const loadingVideo = computed(() => {
    const loaders = ["/images/Sequence_01_final.mp4"];
    return loaders[Math.floor(Math.random() * loaders.length)];
  });

  function embedTwitch() {
    let element = document.getElementById(embedData.value.elementId);
    if (!element || element.children.length === 0) {
      embed.value = new Twitch.Embed(embedData.value.elementId, {
        width: width,
        height: height,
        channel: embedData.value.channel,
        video: embedData.value.video,
        layout: "video",
        autoplay: true,
        muted: isFullWidth,
        controls: false,
        parent: window.location.hostname,
      });

      setupEmbedListeners();
    } else {
      isBuffering.value = false;
      startPlayer();
    }
  }

  function setupEmbedListeners() {
    if (!embed.value) return;

    embed.value.addEventListener(Twitch.Player.PLAY, setIsPlaying);
    embed.value.addEventListener(Twitch.Player.PAUSE, setIsNotPlaying);
    embed.value.addEventListener(Twitch.Player.ENDED, setIsNotPlaying);
    embed.value.addEventListener(
      Twitch.Player.WAITING,
      () => (isBuffering.value = true),
    );
    embed.value.addEventListener(
      Twitch.Player.PLAYING,
      () => (isBuffering.value = false),
    );
    embed.value.addEventListener(Twitch.Player.OFFLINE, () => {
      embedPlaying.value = false;
      isBuffering.value = false;
    });
    embed.value.addEventListener(Twitch.Embed.VIDEO_READY, videoBuffered);
  }

  function videoBuffered() {
    startPlayer();
  }

  function startPlayer() {
    if (!embedPlaying.value) {
      embed.value?.play();
      embed.value?.setMuted(false);
      setIsPlaying();
    }
  }

  function stopPlayer() {
    if (embedPlaying.value) {
      embed.value?.pause();
      setIsNotPlaying();
    }
  }

  function setIsPlaying() {
    embedPlaying.value = true;
  }

  function setIsNotPlaying() {
    embedPlaying.value = false;
  }

  function handleIframeLoad() {
    isBuffering.value = false;
  }

  watch(showTwitchEmbed, (newVal) => {
    if (newVal) embedTwitch();
  });

  onMounted(() => embedTwitch());

  return {
    embed,
    isBuffering,
    embedPlaying,
    showTwitchEmbed,
    startPlayer,
    stopPlayer,
    embedTwitch,
    handleIframeLoad,
    embedData,
    loadingVideo,
  };
}
