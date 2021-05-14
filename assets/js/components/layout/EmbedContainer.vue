<template>
  <div class="transform transition-transform hover:scale-110 px-5">
    <div class="flex flex-row">
      <div v-if="">
        <a :href="link">
          <div v-if="image" :class="image.class" class="p-4" >
            <img :width="image.width" :height="image.height" :src="image.url">
          </div>
        </a>
      </div>
      <div v-if="embedData">
        <component
          :is="embedName"
          v-bind:embedData="embedData"
        ></component>
      </div>
    </div>
    <div class="flex flex-row flex-wrap justify-between pl-4">
      <a :href="link">
        <div class="text-left justify-self-start">{{ title }}</div>
      </a>
    </div>
  </div>
</div>
</template>
<script>
import TwitchEmbed from '../embeds/TwitchEmbed.vue'
import YouTubeEmbed from '../embeds/YouTubeEmbed.vue'

export default {
  name: 'TwitchContainer',
  components: {
    'TwitchEmbed': TwitchEmbed,
    'YouTubeEmbed': YouTubeEmbed,
  },
  props: [
    'title',
    'channelName',
    'rowName',
    'image',
    'link',
    'componentName',
    'embedName',
    'embedData',
  ],
}
</script>
<style scoped>
</style>
