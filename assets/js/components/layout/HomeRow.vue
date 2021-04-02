<template>
  <div @swiped-left="forward()" @swiped-right="back()" class="home-row">
    <div class="text-2xl text-left font-extrabold pl-22 px-12 pt-4 pb-2">{{ settings.title}}</div>
    <div class="flex flex-row justify-start items-center">
      <div>
        <button @click="back()" class="bg-indigo-300 hover:bg-indigo-400 text-gray-800 font-bold p-2 rounded-sm">
          &lt;
        </button>
      </div>
      <div class="flex flex-row mr-5 overflow-x-hidden">
        <div ref="channelDivs" v-for="channel in displayChannels">
          <channel
            v-bind="channel"
            :showChannel = "showChannel(channel)"
            :showThumbnail = "showThumbnail(channel)"
          ></channel>
        </div>
      </div>
      <div>
        <button @click="forward()" class="bg-indigo-300 hover:bg-indigo-400 text-gray-800 font-bold p-2 rounded-sm">
          &gt;
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import Channel from './Channel.vue'
require('swiped-events');

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
      this.rowIndex = (this.rowIndex - 1).mod(this.displayChannels.length);
      this.reorder();
    },
    forward: function() {
      this.rowIndex = (this.rowIndex + 1).mod(this.displayChannels.length);
      this.reorder();
    },
    reorder() {
      for (let i = 0; i < this.$refs.channelDivs.length; i++) {
        let j = (i - this.rowIndex).mod(this.$refs.channelDivs.length);
        // Add one to j because flexbox order should start with 1, not 0
        this.$refs.channelDivs[i].style.order = j + 1;
      }
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
