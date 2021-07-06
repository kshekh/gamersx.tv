<template>
  <div class="transform transition-transform hover:scale-110 px-5">
    <div class="flex flex-row">
      <div v-if="showArt">
        <a :href="link">
          <div v-if="image" :class="image.class" class="p-4" >
            <img :width="image.width" :height="image.height" :src="image.url">
          </div>
        </a>
      </div>
      <div class="embed-frame" v-if="showEmbed && embedData">
        <div class="w-auto p-4" v-on:mouseenter="mouseEntered" v-on:mouseleave="mouseLeft">
          <img v-if="hasOverlay" v-show="displayOverlay" alt="Embed's Custom Overlay" :src="embedData.overlay">
          <component v-show="displayEmbed" ref="embed"
            :is="embedName"
            v-bind:embedData="embedData"
          ></component>
        </div>
      </div>
    </div>
    <div class="fixed inset-x-2">
      <a :href="link">
        <div class="text-left">{{ showOnline ? onlineDisplay.title : offlineDisplay.title }}</div>
      </a>
    </div>
  </div>
</div>
</template>
<script>
import TwitchEmbed from '../embeds/TwitchEmbed.vue'
import YouTubeEmbed from '../embeds/YouTubeEmbed.vue'

export default {
  name: 'EmbedContainer',
  components: {
    'TwitchEmbed': TwitchEmbed,
    'YouTubeEmbed': YouTubeEmbed,
  },
  props: [
    'title',
    'channelName',
    'showOnline',
    'onlineDisplay',
    'offlineDisplay',
    'rowName',
    'image',
    'link',
    'componentName',
    'embedName',
    'embedData',
  ],
  data: function() {
    return {
      displayOverlay: true,
      displayEmbed: false
    };
  },
  methods: {
    mouseEntered: function(e) {
      if (this.hasOverlay) {
        this.displayOverlay = false;
        this.displayEmbed = true;
      }
      this.$refs.embed.startPlayer();
    },
    mouseLeft: function(e) {
      if (this.hasOverlay) {
        this.displayOverlay = true;
        this.displayEmbed = false;
      }
      if (this.$refs.embed.isPlaying()) {
        this.$refs.embed.stopPlayer();
      }
    },
  },
  computed: {
    showEmbed: function() {
      return (this.showOnline && this.onlineDisplay.showEmbed) ||
        (!this.showOnline && this.offlineDisplay.showEmbed)
    },
    showArt: function() {
      return (this.showOnline && this.onlineDisplay.showArt) ||
        (!this.showOnline && this.offlineDisplay.showArt)
    },
    hasOverlay: function() {
      return this.embedData !== null && 'overlay' in this.embedData && this.embedData.overlay;
    },
  },
  mounted: function() {
    this.displayOverlay = this.hasOverlay;
    this.displayEmbed = !this.hasOverlay && this.showEmbed;
  },
}
</script>
