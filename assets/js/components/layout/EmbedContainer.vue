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
      <div v-if="showEmbed && embedData">
        <component
          :is="embedName"
          v-bind:embedData="embedData"
        ></component>
      </div>
    </div>
    <div class="flex flex-row flex-wrap justify-between pl-4">
      <a :href="link">
        <div class="text-left justify-self-start">{{ showOnline ? onlineDisplay.title : offlineDisplay.title }}</div>
      </a>
    </div>
  </div>
</div>
</template>
<script>
import TwitchEmbed from '../embeds/TwitchEmbed.vue'
import YouTubeEmbed from '../embeds/YouTubeEmbed.vue'

export default {
  name: 'TwitchContainer',
  components: {
    'TwitchEmbed': TwitchEmbed,
    'YouTubeEmbed': YouTubeEmbed,
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
}
</script>
<style scoped>
</style>
