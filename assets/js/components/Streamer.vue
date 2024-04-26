<template>
  <div class="container text-center mx-auto">
    <div class="container flex-col">
      <div>
        <h2 class="pb-6 text-4xl font-bold">{{ info.display_name }}</h2>
      </div>
      <div class="flex flex-row align-items-center justify-center space-x-4">

        <div v-bind:style="artBg" class="w-auto p-4">
          <a :href="'https://www.twitch.tv/' + info.display_name" target="_twitch">
            <div class="text-bold">@{{ info.display_name }}</div>
            <div v-if="themeUrls.customArt" class="w-auto p-4" >
              <img :width="300" :height="300" :src="themeUrls.customArt">
            </div>
            <twitch-art v-else-if="info.profile_image_url"
              :imageType="'profile'"
              :src="info.profile_image_url"
            ></twitch-art>
            <div>
              <span v-if="broadcast" class="text-gray-800 bg-red-400 p-1 rounded-sm">
                Live
              </span>
              <span v-else="broadcast" class="text-gray-800 bg-gray-400 p-1 rounded-sm">
                Offline
              </span>
              <span class="font-xl p-2">
                {{ info.follower_count }} followers
              </span >
            </div>
          </a>
        </div>

        <div v-bind:style="embedBg" class="w-auto p-4">
          <twitch-embed v-if="info.login"
            v-bind:embedData="{ elementId: 'twitch-embed-' + info.login, channel: info.login }"
          ></twitch-embed>
          <div v-if="broadcast" class="flex flex-row items-center justify-between">
            <div class="text-lg">{{ broadcast.title }}</div>
            <div class="text-lg ml-auto">{{ broadcast.viewer_count }}</div>
            <div class="pl-4"><img class="inline" src="/images/red-eye.png" ></div>
            </div>
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
            <div v-for="vod in vods" class="rounded-md m-8 p-4">
              <twitch-embed
                v-bind:embedData="{ elementId: 'twitch-embed-vod-' + vod.id, video: vod.id }"
              ></twitch-embed>
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
import TwitchEmbed from './embeds/TwitchEmbed';

export default {
  components: {
    'TwitchArt': TwitchArt,
    'TwitchEmbed': TwitchEmbed
  },
  data: function()  {
    return {
      displayTab: 'recent',
      info: {},
      vods: [],
      broadcast: null,
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
    },
  },
  mounted: function() {
    let dataUrl = this.$el.parentNode.dataset['url'];
    axios
      .get(dataUrl)
      .then(response => {
        this.info = response.data.info;
        this.info.follower_count = response.data.followers;
        this.broadcast = response.data.broadcast.length > 0 ? response.data.broadcast[0] : null;
        this.vods = response.data.vods;
        this.themeUrls = response.data.theme;
      });
    this.show('recent');
  }
}
</script>
<style scoped>
</style>
