<template>
  <div class="transform transition-transform hover:scale-110 px-5">
    <div class="flex flex-row">
      <div v-if="">
        <a :href="link">
          <div v-if="image" :class="image.class" class="p-4" >
            <img :width="image.width" :height="image.height" :src="image.url">
          </div>
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
  name: 'TwitchContainer',
  components: {
    'JsEmbed': JsEmbed,
    'TwitchArt': TwitchArt
  },
  props: [ 'info', 'broadcast', 'rowType', 'rowName', 'sortIndex', 'showChannel', 'image', 'link'],
  computed: {
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
