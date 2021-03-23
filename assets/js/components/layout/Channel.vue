<template>
  <div>
    <div class="flex flex-row bg-indigo-500 rounded-md">
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
    </div>
    <div class="flex flex-row flex-wrap justify-between pl-4">
      <a :href="link">
        <div class="text-left justify-self-start">{{ title }}</div>
      </a>
    </div>
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
      if (this.broadcast !== null) {
        return this.broadcast.user_login;
      } else {
        return this.info.login;
      }
    },
    title: function() {
      if (this.broadcast) {
        return this.broadcast.user_name + ' playing ' + this.broadcast.game_name + ' for ' +
          this.broadcast.viewer_count + ' viewers';
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
