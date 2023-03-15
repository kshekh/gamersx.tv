<template>
  <div @mouseover="showTwitchEmbed = true">
    <div class="h-full w-full" :id="embedDataCopy.elementId"></div>
  </div>
</template>

<script>
export default {
  name: "TwitchEmbed",
  props: {
    embedData: Object,
    height: [Number, String],
    width: [Number, String],
  },
  data: function () {
    return {
      embed: {},
      embedPlaying: false,
      showTwitchEmbed: false,
    };
  },
  methods: {
    embedTwitch: function () {
      console.log("xxx twitch embed");
      this.embed = new Twitch.Embed(this.embedDataCopy.elementId, {
        width: this.width || 540,
        height: this.height || 300,
        channel: this.embedData.channel,
        video: this.embedData.video,
        layout: "video",
        autoplay: true,
        muted: false,
        // controls: false,
        parent: window.location.hostname,
      });

      this.embed.addEventListener(Twitch.Player.PLAY, this.setIsPlaying);
      this.embed.addEventListener(Twitch.Player.PAUSE, this.setIsNotPlaying);
      this.embed.addEventListener(Twitch.Player.ENDED, this.setIsNotPlaying);
      this.embed.addEventListener(Twitch.Player.OFFLINE, this.setIsNotPlaying);
    },
    startPlayer: function () {
      if (!this.embedPlaying && this.showTwitchEmbed) {
        this.embed.play();
        this.embed.setMuted(false);
        this.embedPlaying = true;
      }
    },
    stopPlayer: function () {
      if (this.embedPlaying) {
        this.embed.pause();
        this.embedPlaying = false;
      }
    },
    isPlaying: function () {
      return this.embedPlaying;
    },
    setIsPlaying: function () {
      this.embedPlaying = true;
    },
    setIsNotPlaying: function () {
      this.embedPlaying = false;
    },
  },
  computed: {
    embedDataCopy() {
      return { ...this.embedData };
    },
  },
  watch: {
    showTwitchEmbed(newVal) {
      if (newVal === true) {
        this.embedTwitch();
      }
    },
  },
};
</script>

<style scoped>
</style>
