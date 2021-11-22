<template>
  <div
    class="transform transition-transform hover:scale-110 py-8 px-5"
    v-on:mouseenter="isTitleVisible = true"
    v-on:mouseleave="isTitleVisible = false"
  >
    <div class="flex flex-row">
      <div v-if="showArt">
        <a :href="link">
          <div v-if="image" :class="image.class" class="p-4">
            <img :width="image.width" :height="image.height" :src="image.url" />
          </div>
        </a>
      </div>

      <!-- Show the embed with overlay if there's an embed -->
      <div class="embed-frame" v-if="showEmbed && embedData">
        <div v-on:mouseenter="mouseEntered" v-on:mouseleave="mouseLeft">
          <img
            v-if="overlay"
            v-show="isOverlayVisible"
            alt="Embed's Custom Overlay"
            :src="overlay"
          />
          <component
            v-show="isEmbedVisible"
            ref="embed"
            :is="embedName"
            v-bind:embedData="embedData"
          ></component>
        </div>
      </div>

      <!-- If there's only an overlay, show that instead with a link -->
      <div class="embed-frame" v-else-if="showOverlay">
        <a :href="link">
          <img alt="Embed's Custom Overlay" :src="overlay" />
        </a>
      </div>
    </div>
    <div v-show="isTitleVisible" class="fixed inset-x-2">
      <a :href="link">
        <div class="truncate text-left">
          {{ showOnline ? onlineDisplay.title : offlineDisplay.title }}
        </div>
      </a>
    </div>
  </div>
</template>

<script>
import TwitchEmbed from "../../embeds/TwitchEmbed.vue";
import YouTubeEmbed from "../../embeds/YouTubeEmbed.vue";

export default {
  name: "EmbedContainer",
  components: {
    TwitchEmbed: TwitchEmbed,
    YouTubeEmbed: YouTubeEmbed
  },
  props: [
    "title",
    "channelName",
    "showOnline",
    "onlineDisplay",
    "offlineDisplay",
    "rowName",
    "image",
    "overlay",
    "link",
    "componentName",
    "embedName",
    "embedData"
  ],
  data: function() {
    return {
      isOverlayVisible: true,
      isEmbedVisible: false,
      isTitleVisible: false
    };
  },
  methods: {
    mouseEntered: function(e) {
      if (this.showOverlay) {
        this.isOverlayVisible = false;
        this.isEmbedVisible = true;
      }
      this.$refs.embed.startPlayer();
    },
    mouseLeft: function(e) {
      if (this.showOverlay) {
        this.isOverlayVisible = true;
        this.isEmbedVisible = false;
      }
      if (this.$refs.embed.isPlaying()) {
        this.$refs.embed.stopPlayer();
      }
    }
  },
  computed: {
    showEmbed: function() {
      return (
        (this.showOnline && this.onlineDisplay.showEmbed) ||
        (!this.showOnline && this.offlineDisplay.showEmbed)
      );
    },
    showArt: function() {
      return (
        (this.showOnline && this.onlineDisplay.showArt) ||
        (!this.showOnline && this.offlineDisplay.showArt)
      );
    },
    showOverlay: function() {
      return (
        this.overlay &&
        ((this.showOnline && this.onlineDisplay.showOverlay) ||
          (!this.showOnline && this.offlineDisplay.showOverlay))
      );
    }
  },
  mounted: function() {
    this.isOverlayVisible = this.showOverlay;
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  }
};
</script>
