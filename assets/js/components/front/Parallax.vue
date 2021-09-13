<template>
  <div @swiped-left="forward()" @swiped-right="back()" class="home-row p-6">
    <div class="text-3xl text-left font-bold pl-22 px-12 pt-4 pb-2">{{ settings.title}}</div>
    <div class="flex flex-row justify-start items-center" >
      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="first()">
        <img alt="cursor-left" class="cursor-pointer" v-show="allowScrolling && (rowIndex > 0)" src="/images/left-arrow.png" />
      </div>
      <div ref="channelBox" class="flex flex-row p-5 overflow-hidden">
        <div v-bind:style="customBg(channel)" class="custom-bg relative tall-art" ref="channelDivs" v-for="channel in displayChannels">
            <component class="absolute bottom-6 inset-x-0"
              :is="channel.componentName"
              v-bind="channel"
            ></component>
        </div>
      </div>
      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="forward()">
        <img alt="cursor-right" class="cursor-pointer" v-show="allowScrolling" src="/images/right-arrow.png" />
      </div>
    </div>
  </div>
</template>
<script>
import EmbedContainer from '../layout/EmbedContainer.vue'
import NoEmbedContainer from '../layout/NoEmbedContainer.vue'

require('swiped-events');

export default {
  name: 'Parallax',
  components: {
    'EmbedContainer': EmbedContainer,
    'NoEmbedContainer': NoEmbedContainer,
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
      allowScrolling: false,
    }
  },
  methods: {
    showChannel: function(channel) {
      return (channel.showOnline && (channel.onlineDisplay.showArt || channel.onlineDisplay.showEmbed || channel.onlineDisplay.showOverlay)) ||
        (!channel.showOnline && (channel.offlineDisplay.showArt || channel.offlineDisplay.showEmbed || channel.offlineDisplay.showOverlay));
    },
    first: function() {
      this.rowIndex = 0;
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
    },
    customBg: function(channel) {
      if (channel.customArt) {
        return {
          'backgroundImage': 'url(' + channel.customArt + ')',
        };
      } else {
        return { };
      }
    },
  },
  mounted: function() {
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
  updated: function() {
    this.allowScrolling = this.$refs.channelBox.scrollWidth > this.$refs.channelBox.clientWidth;
  }
}
</script>
<style scoped>
</style>
