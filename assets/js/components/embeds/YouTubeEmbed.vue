<template>
  <div @click="showTwitchEmbed = true" @mouseover="showTwitchEmbed = true">
    <video
    v-if="!showTwitchEmbed || !embed.videoTitle"
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
   
    <div :id="embedDataCopy.elementId"></div>
  </div>
</template>

<script>
export default {
  name: "YouTubeEmbed",
  props: {
    embedData: Object,
    isShowTwitchEmbed: Boolean,
    height: [Number, String],
    width: [Number, String],
  },
  data: function () {
    return {
      embed: {},
      loaders:['/images/buffering_video_06.mp4','/images/buffering_video_07.mp4','/images/buffering_video_10.mp4','/images/buffering_video_11.mp4'],
      embedPlaying: false,
      showTwitchEmbed: false,
      image: "",
      isFirstTimeLoad: true,
    };
  },
  methods: {
    embedYouTube: function () {
      let _this = this;
      window.YT.ready(function() {
        console.log("embedYouTube")
        _this.embed = new YT.Player(_this.embedDataCopy.elementId, {
            width: _this.width || 540,
            height: _this.height || 300,
            videoId: _this.embedDataCopy.video,
            autoplay: false,
            playerVars: {
              modestbranding: true,
              rel: 0,
            },
            events: {
              onStateChange: _this.playerStateChanged,
            },
          });
          // Listen for other players, stop on their start
          _this.$root.$on("yt-embed-playing", _this.stopPlayer);
      })
    },
    playerStateChanged: function (e) {
      if (e.data == YT.PlayerState.PAUSED) {
        this.embedPlaying = false;
      } else if (e.data == YT.PlayerState.PLAYING) {
        // Don't swap the order of these or you'll stop this embed, too
        this.$root.$emit("yt-embed-playing", this.embedDataCopy.elementId);
        this.embedPlaying = true;
      }
    },
    startPlayer: function () {
      console.log('startPlayer from YouTube embed');
      if (this.isFirstTimeLoad) {
        setTimeout(() => {
          if (!this.embedPlaying && this.showTwitchEmbed) {
            if (this.embed) {
              console.log("if",this.embed);
              this.embed.unMute();
              this.embedPlaying = true;
              this.isFirstTimeLoad = false;
            }
          }
        }, 1000);
      } else {
        if (!this.embedPlaying) {
          if (this.embed) {
            this.embed.unMute();
            this.embedPlaying = true;
          }
        }
      }
    },
    stopPlayer: function (elementId) {
      if (
        this.embedPlaying &&
        !(elementId && this.embedDataCopy.elementId === elementId)
      ) {
        this.embed.pauseVideo();
        this.embedPlaying = false;
      }
    },
    isPlaying: function () {
      return this.embedPlaying;
    },
  },
  computed: {
    embedDataCopy() {
      this.image = `https://img.youtube.com/vi/${this.embedData.video}/hqdefault.jpg`;
      return { ...this.embedData };
    },
    loadingVideo(){
      return this.loaders[Math.floor(Math.random()*this.loaders.length)]
    }
  },
  watch: {
    showTwitchEmbed(newVal) {
      if (newVal === true) {
        this.embedYouTube();
      }
    },
    isShowTwitchEmbed(newVal) {
      if (newVal === true) {
        this.showTwitchEmbed = true
      }
    },
  },
};
</script>

<style scoped>
</style>
