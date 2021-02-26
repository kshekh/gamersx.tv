<template>
  <div class="container text-center mx-auto">
    <h1>{{ name }}</h1>
    <div class="flex flex-row space-x-10">
      <div v-for="item in items">
        <div v-if="itemsType == 'streamer'">
          <i-frame-embed
            v-bind:channel="item"
          ></i-frame-embed>
        </div>
        <div v-else>{{ item }}</div>
      </div>
    </div>
  </div>
</template>
<script>
import IFrameEmbed from '../embeds/IFrameEmbed.vue'

export default {
  name: 'HomeRow',
  components: {
    'IFrameEmbed': IFrameEmbed
  },
  props: {
    settings: {
      type: Object,
      required: true,
      default: function() {
        return {
          'name': '',
          'itemsType': '',
          'items': []
        }
      }
    }
  },
  computed: {
    name: function() {
      return this.settings.name;
    },
    items: function() {
      return this.settings.items;
    },
    itemsType: function() {
      return this.settings.itemsType;
    }
  },
  data: function()  {
    return {
      framework : "VueJs"
    }
  },
}
</script>
<style scoped>
</style>
