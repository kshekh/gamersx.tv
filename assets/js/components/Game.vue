<template>
  <div class="container text-center mx-auto">
    <div class="flex-col">
      <div>
        <h2 class="pb-6 text-xl font-bold">Welcome to the Game Page for {{ info.name }} - (Game ID {{ info.id }})</h2>
      </div>
      <div class="flex flex-row align-items-center justify-center space-x-4">

        <div>
          <a :href="'https://www.twitch.tv/directory/game/' + info.name" target="_twitch">
            <div v-if="themeUrls.customArt" class="w-auto p-4" >
              <img :width="225" :height="300" :src="themeUrls.customArt">
            </div>
            <twitch-art v-else-if="info.box_art_url"
              :imageType="'boxArt'"
              :src="info.box_art_url"
            ></twitch-art>
            <p>View on Twitch</p>
          </a>
        </div>

        <div v-bind:style="embedBg" class="w-auto p-4">
          <js-embed v-if="popular.user_login"
            v-bind:channel="popular.user_login">
          </js-embed>
        </div>
      </div>

      <div class="inline-block align-middle" v-if="themeUrls.banner">
        <img class="pt-8" :src="themeUrls.banner" />
      </div>

      <div class="border rounded mt-12">
        <button @click="show('featured')" type="button">
          <span class="text-sm font-light pt-12 p-4">Featured</span>
        </button>
        <button @click="show('all')" type="button">
          <span class="text-sm font-light p-4">All</span>
        </button>
      </div>
      <div>
        <div class="pt-6 font-bold text-xl">
          {{ displayTab }} streams for {{ info.name }}
        </div>
        <div class="flex flex-row flex-grow flex-wrap justify-left p-8">
          <div v-for="stream in streams" class="rounded-md m-8 p-4">
            <js-embed
              v-bind:channel="stream.user_login">
            </js-embed>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import TwitchArt from './layout/TwitchArt';
import JsEmbed from './embeds/JsEmbed';

export default {
  components: {
    'TwitchArt': TwitchArt,
    'JsEmbed': JsEmbed
  },
  data: function()  {
    return {
      displayTab: 'featured',
      info:{},
      streams: {},
      popular: {},
      themeUrls: {}
    }
  },
  methods: {
    show(showType) {
      if (showType === 'featured') {
        this.displayTab = 'Featured';
      } else {
        this.displayTab = 'All';
      }
    }
  },
  computed: {
    embedBg: function() {
      if (this.themeUrls.embedBg) {
        return {
          'backgroundImage': 'url(' + this.themeUrls.embedBg + ')',
        };
      } else {
        return {};
      }
    },
  },
  mounted: function() {
    let dataUrl = this.$el.parentNode.dataset['url'];
    axios
      .get(dataUrl)
      .then(response => {
        this.info = response.data.info;
        this.popular = response.data.streams[0];
        this.streams = response.data.streams.slice(1);
        this.themeUrls = response.data.theme;
      });
    this.show('all');
  }
}
</script>
<style scoped>
</style>
