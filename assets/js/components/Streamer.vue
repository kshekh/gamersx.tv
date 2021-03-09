<template>
  <div class="container text-center mx-auto">
    <div class="container flex-col">
      <h2 class="pb-6 text-xl font-bold">Welcome to the Streamer Page for {{ info.display_name }} - (Streamer ID {{ info.id }})</h2>
      <div class="flex flex-row align-items-center justify-center space-x-4">
        <a :href="'https://www.twitch.tv/' + info.display_name" target="_twitch">
          <twitch-art v-if="info.profile_image_url"
            :imageType="'profile'"
            :src="info.profile_image_url"
          ></twitch-art>
          <p>View on Twitch</p>
        </a>

        <div class="w-auto p-4 bg-indigo-500 rounded-md">
          <p>This is where the broadcast for {{ info.display_name }} will be.</p>
        </div>
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
          <div v-for="x in 8" class="rounded-md w-1/4 h-48 m-8 p-4 bg-indigo-500">This would be a VOD for {{ info.display_name }}</div>
        </div>
      </div>
      <div v-if="displayTab === 'about'">
          <div class="pt-6 font-bold text-xl">
            Here is the Twitch API info for {{ info.display_name }}
          </div>
          <div class="flex flex-row flex-grow flex-wrap justify-center p-8">
          <p class="bg-gray-200 rounded-md text-left text-xl">{{ info }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import TwitchArt from './layout/TwitchArt';

export default {
  components: {
    'TwitchArt': TwitchArt
  },
  data: function()  {
    return {
      displayTab: 'recent',
      info:{},
      message: "Streams",
      numStreams: 8
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
  mounted: function() {
    this.info = JSON.parse(this.$el.parentNode.dataset['info']);
    this.show('recent');
  }
}
</script>
<style scoped>
</style>
