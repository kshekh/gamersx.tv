<template>
  <div class="container text-center mx-auto">
    <div class="flex-col">

      <topic v-if="topicData"
        v-bind="topicData"
      ></topic>

      <div class="border rounded mt-12">
        <button v-for="(tab, index) in tabs" @click="displayTab = index;" type="button">
          <span class="text-sm font-light pt-12 p-4">{{ tab.name }}</span>
        </button>
      </div>

      <div v-for="(tab, index) in tabs">
        <div v-show="displayTab == index">
          <component
            :is="tab.componentName"
            v-bind="tab.data"
          ></component>
        </div>
      </div>

    </div>
  </div>
</template>
<script>
import axios from 'axios';
import Topic from './page/Topic.vue';
import About from './page/About.vue';
import EmbedTab from './page/EmbedTab.vue';

export default {
  components: {
    'Topic': Topic,
    'About': About,
    'EmbedTab': EmbedTab,
  },
  data: function()  {
    return {
      displayTab: 0,
      tabs: [],
      topicData: false,
      themeUrls: {},
    }
  },
  mounted: function() {
    let dataUrl = this.$el.parentNode.dataset['url'];
    axios
      .get(dataUrl)
      .then(response => {
        this.tabs = response.data.tabs;
        this.topicData = response.data.topic;
      });
  }
}
</script>
<style scoped>
</style>
