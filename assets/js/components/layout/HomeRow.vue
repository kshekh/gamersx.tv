<template>
  <div class="container text-center mx-auto">
    <h1>{{ settings.label}}</h1>
    <div class="home-row flex flex-row space-x-10">
      <div v-for="channel in settings.channels">
        <channel
          v-bind="channel"
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
