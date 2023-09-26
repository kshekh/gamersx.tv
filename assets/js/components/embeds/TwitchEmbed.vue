<template>
  <div @mouseover="showTwitchEmbed = true">
    <img
      v-if="image && isBuffering"
      :src="image.url"
      class="relative top-1/2 transform -translate-y-1/2 w-full"
    />
    <img
      v-else-if="overlay && isBuffering"
      alt="Embed's Custom Overlay"
      :src="overlay"
      :class="{'transform -translate-y-1/2':!isMobileDevice,'relative top-1/2 w-full':true}"
    />
    <iframe
      v-if="embedDataCopy.type === 'twitch_clip'"
      :id="embedDataCopy.elementId"
      ref="videoIframe"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
      :src="`${embedDataCopy.url}&autoplay=true`"
      @load="handleIframeLoad"
      width="854"
      height="480"
      frameborder="0"
      allowfullscreen="true"
      scrolling="no"
    ></iframe>
    <div
      v-else
      :id="embedDataCopy.elementId"
      class="h-full w-full m-w-[355px] m-h-[311px]"
      :style="isBuffering ? { display: 'none' } : { display: 'block' }"
    ></div>
  </div>
</template>

<script>
export default {
  name: "TwitchEmbed",
  props: {
    embedData: Object,
    image: Object,
    overlay: String,
    isShowTwitchEmbed: Boolean,
    isMobileDevice: Boolean,
    height: [Number, String],
    width: [Number, String],
    info: {},
    broadcast: {},
  },
  data: function () {
    return {
      embed: {},
      embedPlaying: false,
      showTwitchEmbed: false,
      isBuffering: true,
    };
  },
  methods: {
    embedTwitch: function () {
      let element = document.getElementById(this.embedDataCopy.elementId)
      if (element.children.length == 0) {
        this.embed = new Twitch.Embed(this.embedDataCopy.elementId, {
          width: this.width || 540,
          height: this.height || 300,
          channel: this.embedDataCopy.channel,
          video: this.embedDataCopy.video,
          layout: "video",
          autoplay: true,
          muted: false,
          // controls: false,
          parent: window.location.hostname,
        });
        
        this.embed.addEventListener(Twitch.Player.PLAY, this.setIsPlaying);
        this.embed.addEventListener(Twitch.Player.PAUSE, this.setIsNotPlaying);
        this.embed.addEventListener(Twitch.Player.ENDED, this.setIsNotPlaying);
        this.embed.addEventListener(Twitch.Player.WAITING, () => {
          this.isBuffering = true;
        });
        this.embed.addEventListener(Twitch.Player.PLAYING, () => {
          this.isBuffering = false;
        });
        this.embed.addEventListener(Twitch.Player.OFFLINE, () => {
          this.embedPlaying = false;
          this.isBuffering = false;
        });
      } else {
        this.isBuffering = false;
        this.startPlayer()
      }
    },
    startPlayer: function () {
      if (
        !this.embedPlaying &&
        (this.isShowTwitchEmbed || this.showTwitchEmbed)
      ) {
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
    handlePlayerStateChanged(event) {
      const { video_id, play, play_reason } = event.detail;
      if (video_id === this.embedDataCopy.elementId) {
        if (play && play_reason === "auto") {
          this.embedPlaying = true;
          this.isBuffering = false;
        } else if (!play && play_reason === "buffering") {
          this.embedPlaying = false;
          this.isBuffering = true;
        } else {
          this.embedPlaying = false;
          this.isBuffering = false;
        }
      }
    },
    handleIframeLoad(e) {
      this.isBuffering = false;
    }
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
    isShowTwitchEmbed(newVal) {
      if (newVal === true) {
        this.embedTwitch();
      }
    },
  },
};
</script>

<style scoped>
</style>