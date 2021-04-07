<template>
  <div class="container text-center mx-auto">
    <div class="container flex-col">
      <h2 class="pb-6 text-xl font-bold">Welcome to the Streamer Page for {{ info.display_name }} - (Streamer ID {{ info.id }})</h2>
      <div class="flex flex-row align-items-center justify-center space-x-4">

        <div v-bind:style="artBg" class="w-auto p-4">
          <a :href="'https://www.twitch.tv/' + info.display_name" target="_twitch">
            <twitch-art v-if="info.profile_image_url"
              :imageType="'profile'"
              :src="info.profile_image_url"
            ></twitch-art>
            <p>View on Twitch</p>
          </a>
        </div>

        <div v-bind:style="embedBg" class="w-auto p-4">
          <js-embed v-if="info.login"
            v-bind:channel="info.login">
          </js-embed>
        </div>

      </div>

      <div class="inline-block align-middle" v-if="themeUrls.banner">
        <img class="pt-8" :src="themeUrls.banner" />
      </div>

      <div class="border rounded mt-12">
        <button @click="show('recent')" type="button">
          <span class="text-sm font-light pt-12 p-4">Recent</span>
        </button>
        <button @click="show('about')" type="button">
          <span class="text-sm font-light p-4">About</span>
        </button>
      </div>
      <div v-if="displayTab === 'recent'">
        <div class="pt-6 font-bold text-xl">
          Recent streams for {{ info.display_name }}
        </div>
        <div class="flex flex-row flex-grow flex-wrap justify-left p-8">
          <div v-for="vod in vods" class="rounded-md m-8 p-4 bg-indigo-500">
            <js-embed
              v-bind:video="vod.id">
            </js-embed>
          </div>
        </div>
      </div>
    </div>
    <div v-if="displayTab === 'about'">
      <div class="pt-6 font-bold text-xl">
        <p class="rounded-md text-left text-xl">
          {{ info.description ? info.description : 'This streamer hasn\'t added a description' }}
        </p>
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
      displayTab: 'recent',
      info: {},
      vods: {},
      themeUrls: {},
    }
  },
  methods: {
    show(showType) {
      if (showType === 'recent') {
        this.displayTab = 'recent';
      } else {
        this.displayTab = 'about';
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
    artBg: function() {
      if (this.themeUrls.artBg) {
        return {
          'backgroundImage': 'url(' + this.themeUrls.artBg + ')',
        };
      } else {
        return {};
      }
    }
  },
  mounted: function() {
    let dataUrl = this.$el.parentNode.dataset['url'];
    axios
      .get(dataUrl)
      .then(response => {
        this.info = response.data.info;
        this.vods = response.data.vods;
        this.themeUrls = response.data.theme;
      });
    this.show('recent');
  }
}
</script>
<style scoped>
</style>
