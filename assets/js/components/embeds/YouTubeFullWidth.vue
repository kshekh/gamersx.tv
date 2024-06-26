<template>
  <div>
    <div :id="embedData.elementId"></div>
  </div>
</template>

<script>
import emitter from 'tiny-emitter/instance'

export default {
  name: "YouTubeFullWidth",
  props: {
    embedData: Object,
    height: [Number, String],
    width: [Number, String],
  },
  data: function () {
    return {
      embed: {},
      embedPlaying: false,
    };
  },
  methods: {
    videoBuffered: function () {
      this.startPlayer();

      this.$emit("video-buffered");
    },
    playerStateChanged: function (e) {
      if (e.data == YT.PlayerState.PAUSED) {
        this.setIsNotPlaying();
      } else if (e.data == YT.PlayerState.PLAYING) {
        // Don't swap the order of these or you'll stop this embed, too
        this.$root.$emit("yt-embed-playing", this.embedData.elementId);
        this.setIsPlaying();
      }
    },
    startPlayer: function () {
      if (!this.isPlaying()) {
        this.embed.mute();
        this.embed.playVideo();
        this.setIsPlaying();
      }
    },
    stopPlayer: function (elementId) {
      if (
        this.isPlaying() &&
        !(elementId && this.embedData.elementId === elementId)
      ) {
        this.embed.pauseVideo();
        this.setIsNotPlaying();
      }
    },
    isPlaying: function () {
      return this.embedPlaying;
    },
    setIsPlaying() {
      emitter.emit("set-is-playing", true);
      this.embedPlaying = true;
    },
    setIsNotPlaying() {
      emitter.emit("set-is-playing", false);
      this.embedPlaying = false;
    },
  },
  mounted: function () {
    this.embed = new YT.Player(this.embedData.elementId, {
      width: this.width || 540,
      height: this.height || 300,
      videoId: this.embedData.video,
      autoplay: false,
      playerVars: {
        modestbranding: true,
        rel: 0,
      },
      events: {
        onReady: this.videoBuffered,
        onStateChange: this.playerStateChanged,
      },
    });

    // Listen for other players, stop on their start
    emitter.on("yt-embed-playing", this.stopPlayer);
  },
};
</script>
