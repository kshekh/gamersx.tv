<template>
  <div class="home-row">
    <div class="text-2xl text-left font-extrabold px-12 pt-4 pb-2">{{ settings.title}}
      <div class="inline-flex">
        <button @click="back()" class="bg-indigo-300 hover:bg-indigo-400 text-gray-800 font-bold p-2 rounded-l">
          &lt;
        </button>
        <button @click="forward()" class="bg-indigo-300 hover:bg-indigo-400 text-gray-800 font-bold p-2 rounded-r">
          &gt;
        </button>
      </div>
    </div>
    <div class="flex flex-row overflow-x-hidden space-x-10">
      <div v-for="channel in rowChannels">
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
      rowIndex: 0,
      displayChannels: [],
    }
  },
  computed: {
    rowChannels: function() {
      if (this.rowIndex === 0) {
        return this.displayChannels;
      } else {
        let back = this.displayChannels.slice(this.rowIndex, this.displayChannels.length);
        let front = this.displayChannels.slice(0, this.rowIndex);
        return back.concat(front);
      }
    }
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
    },
    back: function() {
      this.rowIndex = this.rowIndex - 1 % this.displayChannels.length;
    },
    forward: function() {
      this.rowIndex = this.rowIndex + 1 % this.displayChannels.length;
    }
  },
  mounted: function() {
    let displayed = this.settings.channels.filter(function(channel) {
      return this.showThumbnail(channel) || this.showChannel(channel);
    }, this);
    switch (this.settings.sort) {
      case 'asc':
        displayed = displayed.sort(this.sortChannelsAsc);
        break;
      case 'fixed':
        displayed = displayed.sort(this.sortChannelsFixed);
        break;
      case 'desc':
      default:
        displayed = displayed.sort(this.sortChannelsDesc);
    }
    this.rowIndex = 0;
    this.displayChannels = displayed;
  }
}
</script>
<style scoped>
</style>
