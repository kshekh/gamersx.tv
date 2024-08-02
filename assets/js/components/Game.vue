<template>
  <div class="container text-center mx-auto">
    <div class="flex-col">
      <div>
        <!-- Display the game name as a header -->
        <h2 class="pb-6 text-4xl font-bold">{{ info.name }}</h2>
      </div>
      <div class="flex flex-row align-items-center justify-center space-x-4">

        <!-- Display the game art and Twitch link -->
        <div v-bind:style="backgroundStyle('artBg')" class="w-auto p-4">
          <a :href="'https://www.twitch.tv/directory/game/' + info.name" target="_twitch">
            <!-- Custom art if available -->
            <div v-if="themeUrls.customArt" class="w-auto p-4">
              <img :width="225" :height="300" :src="themeUrls.customArt">
            </div>
            <!-- Default Twitch art if custom art is not available -->
            <twitch-art v-else-if="info.box_art_url"
              :imageType="'boxArt'"
              :src="info.box_art_url"
            ></twitch-art>
            <div>
              <!-- Display live streamers count if popular -->
              <span v-if="popular" class="text-gray-800 bg-red-400 p-1 rounded-sm">
                {{ info.streamers }} Live
              </span>
              <!-- Display "No Streams" if not popular -->
              <span v-else class="text-gray-800 bg-gray-400 p-1 rounded-sm">
               No Streams
              </span>
              <!-- Display the number of viewers -->
              <span class="font-xl p-2">
                {{ info.viewers }} watching
              </span >
            </div>
          </a>
        </div>

        <!-- Display the Twitch embed for the popular stream -->
        <div v-bind:style="backgroundStyle('embedBg')" class="w-auto p-4">
          <twitch-embed
            v-if="popular.user_login"
            v-bind:embedData="getEmbedData(popular)">
          </twitch-embed>
          <!-- Display the popular stream title and viewer count -->
          <div class="flex flex-row items-center justify-between">
            <div class="text-lg">{{ popular.title }}</div>
            <div class="text-lg ml-auto">{{ popular.viewer_count }}</div>
            <div class="pl-4">
              <img class="inline" src="/images/red-eye.png" >
            </div>
          </div>
        </div>
      </div>

      <!-- Display the banner image if available -->
      <div class="inline-block align-middle" v-if="themeUrls.banner">
        <img class="pt-8" :src="themeUrls.banner" />
      </div>

      <!-- Buttons to switch between "Featured" and "All" streams -->
      <div class="border rounded mt-12">
        <button @click="show('featured')" type="button">
          <span class="text-sm font-light pt-12 p-4">Featured</span>
        </button>
        <button @click="show('all')" type="button">
          <span class="text-sm font-light p-4">All</span>
        </button>
      </div>

      <!-- Display the streams based on the selected tab -->
      <div>
        <div class="pt-6 font-bold text-xl">
          {{ displayTab }} streams for {{ info.name }}
        </div>
        <div class="flex flex-row flex-grow flex-wrap justify-left p-8">
          <!-- Loop through the streams and display each as a Twitch embed -->
          <div v-for="stream in streams" class="rounded-md m-8 p-4">
            <twitch-embed  v-bind:embedData="getEmbedData(stream)"></twitch-embed>
          </div>
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
      displayTab: 'featured', // The current tab being displayed
      info: {}, // Information about the game
      streams: {}, // List of streams for the game
      popular: {}, // The most popular stream
      themeUrls: {} // URLs for custom themes
    }
  },
  methods: {
    // Switch between "Featured" and "All" streams
    show(showType) {
      if (showType === 'featured') {
        this.displayTab = 'Featured';
      } else {
        this.displayTab = 'All';
      }
    },
    // Get embed data for a stream
    getEmbedData(stream) {
      return {
        channel: stream.user_login,
        elementId: 'twitch-embed-' + stream.user_login,
      };
    },
  },
  computed: {
    // Computed property for backgrounds
    backgroundStyle() {
      return (bgType) => {
        if (this.themeUrls[bgType]) {
          return {
            'backgroundImage': 'url(' + this.themeUrls[bgType] + ')',
          };
        } else {
          return {};
        }
      };
    }
  },
  mounted: function() {
    // Fetch data when the component is mounted
    let dataUrl = this.$el.parentNode.dataset['url'];
    axios
      .get(dataUrl)
      .then(response => {
        // Set the component data with the response data
        this.info = response.data.info;
        this.info.streamers = response.data.streamers;
        this.info.viewers = response.data.viewers;
        this.popular = response.data.streams[0];
        this.streams = response.data.streams.slice(1);
        this.themeUrls = response.data.theme;
      });
    this.show('all'); // Default to showing all streams
  }
}
</script>