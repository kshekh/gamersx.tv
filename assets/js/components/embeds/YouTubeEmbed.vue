<template>
  <div>
    <div :id="embedData.elementId"></div>
  </div>
</template>

<script>
export default {
  name: 'YouTubeEmbed',
  props: {
    embedData: Object,
    height: [Number, String],
    width: [Number, String],
  },
  data: function() {
    return {
      embed: {},
      embedPlaying: false,
    }
  },
  methods: {
    playerStateChanged: function(e) {
      if (e.data == YT.PlayerState.PAUSED) {
        this.embedPlaying = false;
      } else if (e.data == YT.PlayerState.PLAYING) {
        // Don't swap the order of these or you'll stop this embed, too
        this.$root.$emit('yt-embed-playing', this.embedData.elementId);
        this.embedPlaying = true;
      }
    },
    startPlayer: function() {
      if (!this.embedPlaying) {
        this.embed.playVideo();
        this.embed.unMute();
        this.embedPlaying = true;
      }
    },
    stopPlayer: function(elementId) {
      if (this.embedPlaying && !(elementId && this.embedData.elementId === elementId)) {
        this.embed.pauseVideo();
        this.embedPlaying = false;
      }
    },
    isPlaying: function() {
      return this.embedPlaying;
    },
  },
  mounted: function() {
    this.embed = new YT.Player(this.embedData.elementId, {
      width: this.width || 540,
      height: this.height || 300,
      videoId: this.embedData.video,
      autoplay: false,
      playerVars: {
        'modestbranding': true,
        'rel': 0,
      },
      events: {
        'onStateChange': this.playerStateChanged,
      },
    });

    // Listen for other players, stop on their start
    this.$root.$on('yt-embed-playing', this.stopPlayer);
  }
}
</script>

<style scoped>
</style>
