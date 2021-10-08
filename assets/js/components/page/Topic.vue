<template>
  <div>
    <div>
      <h2 class="pb-6 text-4xl font-bold">{{ title }}</h2>
    </div>

    <div class="flex flex-row align-items-center justify-center space-x-4">

      <div v-bind:style="artBg" class="w-auto p-4">
        <a :href="'https://www.twitch.tv/directory/game/' + title" target="_twitch">
          <div v-if="theme.customArt" class="w-auto p-4" >
            <img :width="225" :height="300" :src="theme.customArt">
          </div>
          <div :class="image.class" class="p-4" >
            <img :width="image.width" :height="image.height" :src="image.url">
          </div>
          <div>
            <span class="text-gray-800 bg-twitch p-1 rounded-sm">
              {{ title }}
            </span>
          </div>
        </a>
      </div>

      <div v-bind:style="embedBg" class="w-auto p-4">
        <component
          :is="embed.componentName"
          v-bind="embed"
        ></component>

      </div>

    </div>

    <div class="inline-block align-middle" v-if="theme.banner">
      <img class="pt-8" :src="theme.banner" />
    </div>

  </div>
</template>
<script>
import EmbedContainer from '../layout/EmbedContainer/EmbedContainer.vue'

export default {
  name: 'Topic',
  components: {
    'EmbedContainer': EmbedContainer,
  },
  props: [ 'theme', 'image', 'title', 'embed' ],
  computed: {
    embedBg: function() {
      if (this.theme.embedBg) {
        return {
          'backgroundImage': 'url(' + this.theme.embedBg + ')',
        };
      } else {
        return {};
      }
    },
    artBg: function() {
      if (this.theme.artBg) {
        return {
          'backgroundImage': 'url(' + this.theme.artBg + ')',
        };
      } else {
        return {};
      }
    },
  },
}
</script>
