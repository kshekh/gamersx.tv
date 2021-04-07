<template>
  <div class="px-5">
    <div class="flex flex-row">
      <div v-if="showThumbnail">
        <a :href="link">
          <twitch-art
            :imageType="imageType"
            :src="image"
          ></twitch-art>
        </a>
      </div>
      <div v-if="showChannel">
        <js-embed
          v-bind:elementId="elementId"
          v-bind:channel="channel"
        ></js-embed>
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
import JsEmbed from '../embeds/JsEmbed.vue'
import TwitchArt from './TwitchArt.vue'

export default {
  name: 'Channel',
  components: {
    'JsEmbed': JsEmbed,
    'TwitchArt': TwitchArt
  },
  props: [ 'info', 'broadcast', 'rowType', 'rowName', 'sortIndex', 'showThumbnail', 'showChannel', 'showArt', 'offlineDisplayType', 'linkType' ],
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
            case 'game': return 'https://www.twitch.tv/directory/game/' + this.info.name;
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
    },
    elementId: function() {
      return this.slugify('twitch-embed-' + this.channel + '-' + this.rowName);
    }
  },
  methods: {
    /* Used to prevent duplicate twitch-embed IDs.
    From a public gist found via: https://mhagemann.medium.com/the-ultimate-way-to-slugify-a-url-string-in-javascript-b8e4a0d849e1 */
    slugify: function(s) {
      const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõőṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;'
      const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnoooooooooprrsssssttuuuuuuuuuwxyyzzz------'
      const p = new RegExp(a.split('').join('|'), 'g')

      return s.toString().toLowerCase()
        .replace(/\s+/g, '-') // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, '-and-') // Replace & with 'and'
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters
        .replace(/\-\-+/g, '-') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
    },
  }
}
</script>
<style scoped>
</style>
