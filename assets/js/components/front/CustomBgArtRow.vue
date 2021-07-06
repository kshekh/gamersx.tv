<template>
  <div class="w-full" v-bind:style="customBg">

    <div @swiped-left="forward()" @swiped-right="back()" class="home-row bottom-0 p-6">
      <div v-if="image" :class="image.class" class="p-4" >
        <img :width="image.width" :height="image.height" :src="image.url">
      </div>
      <div class="text-3xl text-left font-bold pl-22 px-12 pt-4 pb-2">{{ settings.title}}</div>
      <div class="flex flex-row justify-start items-center">
        <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="back()">
          <img alt="cursor-left" class="cursor-pointer" src="/images/left-arrow.png" />
        </div>
      <div ref="channelBox" class="flex flex-row p-5 overflow-hidden">
        <div ref="channelDivs" v-for="channel in displayChannels">
          <component
            :is="channel.componentName"
            v-if="showChannel(channel)"
            v-bind="channel"
          ></component>
        </div>
      </div>
      <div class="w-16 h-16 flex-shrink-0 flex-grow-0" @click="forward()">
        <img alt="cursor-right" class="cursor-pointer" src="/images/right-arrow.png" />
      </div>

    </div>
  </div>
</div>
</template>
<script>
import EmbedContainer from '../layout/EmbedContainer.vue'

require('swiped-events');

export default {
  name: 'CustomBgArtRow',
  components: {
    'EmbedContainer': EmbedContainer,
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
    customBg: function() {
      let selected = this.displayChannels[this.rowIndex];
      if (selected && selected.customArt) {
        return {
          'backgroundImage': 'url(' + selected.customArt + ')',
        };
      } else {
        return {};
      }
    },
  },
  methods: {
    showChannel: function(channel) {
      return (channel.showOnline && (channel.onlineDisplay.showArt || channel.onlineDisplay.showEmbed)) ||
        (!channel.showOnline && (channel.offlineDisplay.showArt || channel.offlineDisplay.showEmbed));
    },
    first: function() {
      this.rowIndex = 0;
      this.reorder();
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
    this.displayChannels = this.settings.channels.filter(this.showChannel);
  },
}
</script>
<style scoped>
</style>
