<template>
  <div>
    <div v-if="image">
      <a v-if="link" :href="link">
      <twitch-art
        :imageType="imageType"
        :src="image"
      ></twitch-art>
    </a>
    </div>
    <div v-if="channel">
      <i-frame-embed
        v-bind:channel="channel"
      ></i-frame-embed>
    </div>
  </div>
</template>
<script>
import IFrameEmbed from '../embeds/IFrameEmbed.vue'
import TwitchArt from './TwitchArt.vue'

export default {
  name: 'Channel',
  components: {
    'IFrameEmbed': IFrameEmbed,
    'TwitchArt': TwitchArt
  },
  props: [ 'channel', 'image', 'imageType', 'link', 'showEmbed' ],
}
</script>
<style scoped>
</style>
