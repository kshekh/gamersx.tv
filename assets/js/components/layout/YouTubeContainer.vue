<template>
  <div class="transform transition-transform hover:scale-110 px-5">
    <div class="flex flex-row">
      <div v-if="showThumbnail">
        <a :href="link">
          <v-if="false" twitch-art
            :imageType="imageType"
            :src="image"
          ></twitch-art>
        </a>
      </div>
      <div v-if="showChannel">
        <you-tube-embed
          v-bind:elementId="elementId"
          v-bind:video="videoId"
        ></you-tube-embed>
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
import YouTubeEmbed from '../embeds/YouTubeEmbed.vue'
import TwitchArt from './TwitchArt.vue'

export default {
  name: 'YouTubeContainer',
  components: {
    'YouTubeEmbed': YouTubeEmbed,
    'TwitchArt': TwitchArt
  },
  props: [ 'info', 'broadcast', 'rowType', 'rowName', 'sortIndex', 'showThumbnail', 'showChannel', 'showArt', 'offlineDisplayType', 'linkType' ],
  computed: {
    imageType: function() {
      return 'profile';
    },
    image: function() {
      return this.broadcast.thumbnails.url;
    },
    link: function() {
      return 'http://youtube.com/watch?v=' + this.broadcast.id;
    },
    channel: function() {
      return this.broadcast.channelName;
    },
    videoId: function() {
      return this.broadcast?.id?.videoId ?? this.broadcast.id
    },
    title: function() {
      return this.broadcast.title;
    },
    elementId: function() {
      return this.slugify('youtube-embed-' + this.channel + '-' + this.rowName);
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
