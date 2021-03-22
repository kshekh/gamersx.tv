<template>
  <div>
    <div v-if="showThumbnail">
      <a :href="link">
        <twitch-art
          :imageType="imageType"
          :src="image"
        ></twitch-art>
      </a>
    </div>
    <div v-if="showChannel">
      <i-frame-embed
        v-bind:channel="channel"
      ></i-frame-embed>
    </div>
    <div v-if="showThumbnail || showChannel" class="stream-label">
    <a :href="link">
      <span>{{ title }}</span>
    </a>
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
      switch(this.linkType) {
        case 'gamersx':
          switch(this.rowType) {
            case 'streamer': return '/streamer/' + this.info.id;
            case 'game': return '/game/' + this.info.id;
          }
        case 'twitch':
          switch(this.rowType) {
            case 'streamer': return 'https://www.twitch.tv/' + this.info.login;
            case 'game': return 'https://www.twitch.tv/directory/game/' + this.info.game_name;
          }
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
  props: [ 'info', 'broadcast', 'rowType', 'sortIndex', 'showThumbnail', 'showChannel', 'showArt', 'offlineDisplayType', 'linkType' ],
}
</script>
<style scoped>
</style>
