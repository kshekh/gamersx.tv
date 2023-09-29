<template>
  <div @click="showTwitchEmbed = true" @mouseover="showTwitchEmbed = true">
    <img
      v-if="!showTwitchEmbed || !embed.videoTitle"
      :src="image"
      alt="Image missing"
      :style="{ height: height, width: width }"
    />
    <div :id="embedDataCopy.elementId"></div>
  </div>
</template>

<script>
export default {
  name: "YouTubeEmbed",
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
      image: "",
      isFirstTimeLoad: true,
    };
  },
  methods: {
    embedYouTube: function () {
      this.embed = new YT.Player(this.embedDataCopy.elementId, {
        width: this.width || 540,
        height: this.height || 300,
        videoId: this.embedDataCopy.video,
        autoplay: true,
        playerVars: {
          modestbranding: true,
          rel: 0,
        },
        events: {
          onStateChange: this.playerStateChanged,
        },
      });
      this.startPlayer();
      // Listen for other players, stop on their start
      this.$root.$on("yt-embed-playing", this.stopPlayer);
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
      if (this.isFirstTimeLoad) {
        setTimeout(() => {
          if (!this.embedPlaying && this.showTwitchEmbed) {
            if (this.embed) {
              this.embed.playVideo();
              this.embed.unMute();
              this.embedPlaying = true;
              this.isFirstTimeLoad = false;
            }
          }
        }, 1000);
      } else {
        if (!this.embedPlaying) {
          if (this.embed) {
            this.embed.playVideo();
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
  },
  watch: {
    showTwitchEmbed(newVal) {
      if (newVal === true) {
        this.embedYouTube();
      }
    },
  },
};
</script>

<style scoped>
</style>
