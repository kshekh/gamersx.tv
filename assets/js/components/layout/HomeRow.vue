<template>
  <div class="home-row">
    <div class="text-2xl text-left font-extrabold px-12 pt-4 pb-2">{{ settings.title}}</div>
    <div class="flex flex-row overflow-x-auto space-x-10">
      <div v-for="channel in displayChannels">
        <channel
          v-bind="channel"
          :showChannel = "showChannel(channel)"
          :showThumbnail = "showThumbnail(channel)"
        ></channel>
      </div>
    </div>
  </div>
</template>
<script>
import Channel from './Channel.vue'

export default {
  name: 'HomeRow',
  components: {
    'Channel': Channel,
  },
  props: {
    settings: {
      type: Object,
      required: true,
    }
  },
  data: function()  {
    return {
      framework : "VueJs"
    }
  },
  computed: {
    displayChannels: function() {
      let displayed = this.settings.channels.filter(function(channel) {
        return this.showThumbnail(channel) || this.showChannel(channel);
      }, this);
      switch (this.settings.sort) {
        case 'asc': return displayed.sort(this.sortChannelsAsc);
        case 'desc': return displayed.sort(this.sortChannelsDesc);
        case 'fixed': return displayed.sort(this.sortChannelsFixed);
      }
    },
  },
  methods: {
    showThumbnail: function(channel) {
      return channel.showArt || (channel.broadcast === null && channel.offlineDisplayType === 'art');
    },
    showChannel: function(channel) {
      return channel.broadcast != null || channel.offlineDisplayType == 'stream';
    },
    // Sort with the least popular first
    sortChannelsAsc: function(first, second) {
      return -1 * this.sortChannelsDesc(first, second);
    },
    // Sort with the most popular first
    sortChannelsDesc: function(first, second) {
      if (first.broadcast != null) {
        if (second.broadcast === null) {
          return -1 // only first is broadcasting
        } else {
          return second.broadcast.viewer_count - first.broadcast.viewer_count;
        }
      } else if (second.broadcast != null) {
        return 1; //only second if broadcasting
      } else { // nobody broadcasting
        return second.info.view_count - first.info.view_count;
      }
    },
    // Sort by the given index
    sortChannelsFixed: function(first, second) {
      return first.sortIndex - second.sortIndex;
    }
  }
}
</script>
<style scoped>
</style>
