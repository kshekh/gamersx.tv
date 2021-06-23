<template>
  <div class="container text-center mx-auto">
    <div class="flex-col">

      <topic v-if="topicData"
        v-bind="topicData"
      ></topic>

      <div class="border-b-2 mt-12">
        <button v-for="(tab, index) in tabs" @click="displayTab = index;" type="button"
          v-bind:class="{ 'bg-twitch text-black': isActiveTab(index)  }" class="px-4 pt-4 pb-2 border-2 border-b-0 rounded-t-xl" >
          <span class="text-lg font-light p-4">{{ tab.name }}</span>
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
  methods: {
    isActiveTab: function(tabIndex) {
      return tabIndex == this.displayTab;
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
