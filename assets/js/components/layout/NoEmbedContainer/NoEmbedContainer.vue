<template>
  <div class="transform transition-transform hover:scale-110 py-8 px-5" >
    <div class="flex flex-row">

      <div>
        <a :href="link">
          <div v-if="image" :class="image.class" class="p-4" >
            <img :width="image.width" :height="image.height" :src="image.url">
          </div>
        </a>
      </div>

      <div class="embed-frame">
        <a :href="link">
          <img alt="Embed's Custom Overlay" :src="overlay">
        </a>
      </div>

    </div>
    <div v-show="isTitleVisible" class="fixed inset-x-2">
      <a :href="link">
        <div class="truncate text-left">{{ showOnline ? onlineDisplay.title : offlineDisplay.title }}</div>
      </a>
    </div>
  </div>
</template>
<script>

export default {
  name: 'NoEmbedContainer',
  props: [
    'title',
    'channelName',
    'showOnline',
    'onlineDisplay',
    'offlineDisplay',
    'rowName',
    'image',
    'overlay',
    'link',
    'componentName',
    'embedName',
    'embedData',
  ],
  data: function() {
    return {
      isOverlayVisible: true,
      isEmbedVisible: false,
      isTitleVisible: false,
    };
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
    showOverlay: function() {
      return this.overlay &&
        ((this.showOnline && this.onlineDisplay.showOverlay) ||
        (!this.showOnline && this.offlineDisplay.showOverlay));
    }
  },
  mounted: function() {
    this.isOverlayVisible = this.showOverlay;
    this.isEmbedVisible = this.showEmbed && !this.isOverlayVisible;
  },
}
</script>
