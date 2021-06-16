<template>
  <div class="w-auto p-4">
    <div :id="embedData.elementId"></div>
  </div>
</template>

<script>
export default {
  name: 'YouTubeEmbed',
  props: {
    embedData: Object,
    elementId: String,
  },
  data: function() {
    return {
      height: 300,
      width: 540,
      embed: {},
      isPlaying: false,
    }
  },
  methods: {
    playerStateChanged: function(e) {
      if (e.data == YT.PlayerState.PAUSED) {
        this.isPlaying = false;
      } else if (e.data == YT.PlayerState.PLAYING) {
        // Don't swap the order of these or you'll stop this embed, too
        this.$root.$emit('yt-embed-playing');
        this.isPlaying = true;
      }
    },
    stopPlayer: function() {
      if (this.isPlaying) {
        this.embed.pauseVideo();
        this.isPlaying = false;
      }
    },
  },
  mounted: function() {
    this.embed = new YT.Player(this.embedData.elementId, {
      width: this.width,
      height: this.height,
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
