<template>
  <div :id="embedData.elementId"></div>
</template>

<script>
// We will use props to configure later
export default {
  name: "TwitchEmbed",
  props: {
    embedData: Object,
    height: [Number, String],
    width: [Number, String],
  },
  data: function() {
    return {
      embed: {},
      embedPlaying: false
    };
  },
  methods: {
    videoBuffered: function () {
      this.startPlayer();

      this.$emit('video-buffered');
    },
    startPlayer: function() {
      if (!this.isPlaying()) {
        this.embed.play();
        this.setIsPlaying();
      }
    },
    stopPlayer: function() {
      if (this.isPlaying) {
        this.embed.pause();
        this.setIsNotPlaying();
      }
    },
    isPlaying: function() {
      return this.embedPlaying;
    },
    setIsPlaying() {
      this.$emit('set-is-playing', true)
      this.embedPlaying = true;
    },
    setIsNotPlaying() {
      this.$emit('set-is-playing', false)
      this.embedPlaying = false;
    }
  },
  mounted: function() {
    this.embed = new Twitch.Embed(this.embedData.elementId, {
      width: this.width || 540,
      height: this.height || 300,
      channel: this.embedData.channel,
      video: this.embedData.video,
      layout: "video",
      autoplay: false,
      muted: true,
      controls: true,
      parent: window.location.hostname
    });

    this.embed.addEventListener(Twitch.Player.PLAY, this.setIsPlaying);
    this.embed.addEventListener(Twitch.Player.PAUSE, this.setIsNotPlaying);
    this.embed.addEventListener(Twitch.Player.ENDED, this.setIsNotPlaying);
    this.embed.addEventListener(Twitch.Player.OFFLINE, this.setIsNotPlaying);

    this.embed.addEventListener(Twitch.Embed.VIDEO_READY, this.videoBuffered);
  }
};
</script>

<style scoped></style>
