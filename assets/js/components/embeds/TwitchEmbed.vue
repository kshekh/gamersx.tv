<template>
  <div @mouseover="showTwitchEmbed = true">
    <img
      v-if="!!(image && isBuffering)"
      :src="image['url']"
      class="relative top-1/2 transform -translate-y-1/2 w-full"
    />
    <video
      v-else-if="!!(overlay && isBuffering)"
      autoplay="autoplay"
      muted="muted"
      loop="loop"
      playsinline=""
      class="h-full md:w-full object-cover"
    >
      <source
        :src="loadingVideo"
        type="video/mp4"
      />
    </video>
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
      loaders:['/images/Sequence_01_final.mp4'],
      embedPlaying: false,
      showTwitchEmbed: false,
      isBuffering: true,
    };
  },
  methods: {
    embedTwitch: function () {
      let element = document.getElementById(this.embedDataCopy.elementId);
      if (element.children.length === 0) {
        this.embed = new Twitch.Embed(this.embedDataCopy.elementId, {
          width: this.width || 540,
          height: this.height || 300,
          channel: this.embedDataCopy.channel,
          video: this.embedDataCopy.video,
          layout: "video",
          autoplay: true,
          muted: false,
          controls: false,
          parent: window.location.hostname,
        });
        console.log('the embed created ', this.embed)
        this.embed.addEventListener(Twitch.Player.PLAY, () => {
          console.log('PLAY event triggered');
          this.setIsPlaying();
        });
        this.embed.addEventListener(Twitch.Player.PAUSE, () => {
          console.log('PAUSE event triggered');
          this.setIsNotPlaying();
        });
        this.embed.addEventListener(Twitch.Player.ENDED, () => {
          console.log('ENDED event triggered');
          this.setIsNotPlaying();
        });
        this.embed.addEventListener(Twitch.Player.WAITING, () => {
          console.log('WAITING event triggered');
          this.isBuffering = true;
        });
        this.embed.addEventListener(Twitch.Player.PLAYING, () => {
          console.log('PLAYING event triggered');
          this.isBuffering = false;
          console.log('i set is buffering to ', this.isBuffering)
        });
        this.embed.addEventListener(Twitch.Player.OFFLINE, () => {
          console.log('OFFLINE event triggered');
          this.embedPlaying = false;
          this.isBuffering = false;
        });
        console.log('is it still buffering? ', this.isBuffering);
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
        console.log('details regarding the embed', this.embed);
        this.embed?.play();
        this.embed?.setMuted(false);
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
    loadingVideo(){
      return this.loaders[Math.floor(Math.random()*this.loaders.length)]
    }
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