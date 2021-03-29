<template>
  <div class="container text-center mx-auto">
    <div class="container flex-col">
      <h2 class="pb-6 text-xl font-bold">Welcome to the Game Page for {{ info.name }} - (Game ID {{ info.id }})</h2>
      <div class="flex flex-row align-items-center justify-center space-x-4">
        <div>
        <a :href="'https://www.twitch.tv/directory/game/' + info.name" target="_twitch">
          <twitch-art v-if="info.box_art_url"
            :imageType="'boxArt'"
            :src="info.box_art_url"
          ></twitch-art>
          <p>View on Twitch</p>
        </a>
      </div>

      <div>
        <div class="w-auto p-4 bg-indigo-500 rounded-md">
          <p>This is where the most popular stream for {{ info.name }} will be.</p>
        </div>
      </div>
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
        <div class="flex flex-row flex-grow flex-wrap justify-center p-8">
          <div v-for="x in numStreams" class="rounded-md w-1/3 h-96 m-8 p-4 bg-indigo-500">{{ message }}</div>
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
      displayTab: 'featured',
      info:{},
      message: "Streams",
      numStreams: 2
    }
  },
  methods: {
    show(showType) {
      if (showType === 'featured') {
        this.displayTab = 'Featured';
        this.message = "This is a featured stream for " + this.info.name;
        this.numStreams = 2;
      } else {
        this.displayTab = 'All';
        this.message = "One of many streams for " + this.info.name;
        this.numStreams = 4;
      }
    }
  },
  mounted: function() {
    this.info = JSON.parse(this.$el.parentNode.dataset['info']);
    this.show('featured');
  }
}
</script>
<style scoped>
</style>
