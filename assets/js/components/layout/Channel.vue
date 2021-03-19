<template>
  <div>
    <div v-if="image">
      <a :href="link">
        <twitch-art
          v-if="this.broadcast === null"
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
    <span>{{ title }}</span>
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
        case 'streamer': return '/streamer/' + this.info.id;
        case 'game': return '/game/' + this.info.id;
      }
    },
    channel: function() {
      return this?.broadcast?.user_login;
    },
    title: function() {
      if (this.broadcast) {
        return this.broadcast.user_name + ' playing ' + this.broadcast.game_name;
      } else {
        switch(this.rowType) {
          case 'streamer': return this.info.display_name;
          case 'game': return this.info.name;
        }
      }
    }

  },
  props: [ 'info', 'broadcast', 'rowType', 'sortIndex', 'showArt', 'offlineDisplayType', 'linkType' ],
}
</script>
<style scoped>
</style>
