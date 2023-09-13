<template>
  <div>
    <div v-if="isBuffering" :style="customBg"></div>
    <div
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
      class="h-full w-full"
      :id="embedData.elementId"
    ></div>
  </div>
</template>

<script>
// We will use props to configure later
export default {
  name: "TwitchEmbed",
  props: {
    embedData: Object,
    customBg: Object,
    height: [Number, String],
    width: [Number, String],
  },
  data: function () {
    return {
      embed: {},
      embedPlaying: false,
      isBuffering: true,
    };
  },
  methods: {
    videoBuffered: function () {
      this.startPlayer();

      this.$emit("video-buffered");
    },
    startPlayer: function () {
      if (!this.isPlaying()) {
        this.embed.play();
        this.setIsPlaying();
      }
    },
    stopPlayer: function () {
      if (this.isPlaying) {
        this.embed.pause();
        this.setIsNotPlaying();
      }
    },
    isPlaying: function () {
      return this.embedPlaying;
    },
    setIsPlaying() {
      this.$emit("set-is-playing", true);
      this.embedPlaying = true;
    },
    setIsNotPlaying() {
      this.$emit("set-is-playing", false);
      this.embedPlaying = false;
    },
    embedTwitch() {
      console.log('this.isBuffering 11', this.isBuffering);
      this.embed = new Twitch.Embed(this.embedData.elementId, {
        width: this.width || 540,
        height: this.height || 300,
        channel: this.embedData.channel,
        video: this.embedData.video,
        layout: "video",
        autoplay: true,
        muted: true,
        controls: true,
        parent: window.location.hostname,
      });

      this.embed.addEventListener(Twitch.Player.PLAY, this.setIsPlaying);
      this.embed.addEventListener(Twitch.Player.PAUSE, this.setIsNotPlaying);
      this.embed.addEventListener(Twitch.Player.ENDED, this.setIsNotPlaying);
      this.embed.addEventListener(Twitch.Player.WAITING, () => {
        console.log('this.isBuffering 2', this.isBuffering);
        this.isBuffering = true;
        console.log('this.isBuffering 22', this.isBuffering);
      });
      this.embed.addEventListener(Twitch.Player.PLAYING, () => {
        console.log('this.isBuffering 3', this.isBuffering);
        this.isBuffering = false;
        console.log('this.isBuffering 33', this.isBuffering);
      });
      this.embed.addEventListener(Twitch.Player.OFFLINE, () => {
        console.log('this.embedPlaying 4', this.embedPlaying);
        console.log('this.isBuffering 5', this.isBuffering);
        this.embedPlaying = false;
        this.isBuffering = false;
        console.log('this.embedPlaying 44', this.embedPlaying);
        console.log('this.isBuffering 55', this.isBuffering);
      });
      this.embed.addEventListener(Twitch.Embed.VIDEO_READY, () => {
        console.log('this.isBuffering 6', this.isBuffering);
        this.isBuffering = false;
        this.videoBuffered;
        console.log('this.isBuffering 66', this.isBuffering);
      });
    },
  },
  mounted: function () {
    this.embedTwitch();
  },
};
</script>

<style scoped></style>
