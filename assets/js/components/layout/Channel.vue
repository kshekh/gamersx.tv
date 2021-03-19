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
  computed: {
    imageType: function() {
      switch(this.rowType) {
        case 'streamer': return 'profile';
        case 'game': return 'boxArt';
      }
    },
    image: function() {
      switch(this.rowType) {
        case 'streamer': return this.info.profile_image_url;
        case 'game': return this.info.box_art_url;
      }
    },
    link: function() {
      switch(this.rowType) {
        case 'streamer': return '/game/' + this.info.id;
        case 'game': return '/streamer/' + this.info.id;
      }
    }
  },
  props: [ 'info', 'broadcast', 'rowType', 'channel', 'showEmbed' ],
}
</script>
<style scoped>
</style>
