<template>
  <div class="text-center mx-auto">
    <div class="divide-y-4 divide-current my-4 flex-col">
      <div
        v-for="row in settings.rows">
        <component
          :is="row.componentName"
          v-bind:settings="row"
        ></component>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import ClassicSm from './front/ClassicSm.vue';
import ClassicMd from './front/ClassicMd.vue';
import ClassicLg from './front/ClassicLg.vue';
import FullWidthDescriptive from './front/FullWidthDescriptive.vue';
import FullWidthImagery from './front/FullWidthImagery.vue';
import Parallax from './front/Parallax.vue';
import NumberedRow from './front/NumberedRow.vue';

export default {
  components: {
    'ClassicSm': ClassicSm,
    'ClassicMd': ClassicMd,
    'ClassicLg': ClassicLg,
    'FullWidthDescriptive': FullWidthDescriptive,
    'FullWidthImagery': FullWidthImagery,
    'Parallax': Parallax,
    'NumberedRow': NumberedRow,
  },
  data: function() {
    return {
      settings: {
        rows: []
      },
      pollingApiData: null,
      requestPollingDelay: 30000
    }
  },
  methods: {
    requestHomeApi: function() {
      axios
        .get('/home/api')
        .then(response => {
          this.settings = response.data.settings;
        })
    }
  },
  mounted: function() {
    this.requestHomeApi()

    this.pollingApiData = setInterval(() => {
      this.requestHomeApi()
    }, this.requestPollingDelay)
  },
  beforeDestroy: function() {
    clearInterval(this.pollingApiData)
  }
}

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function(n) {
  return ((this % n) + n) % n;
}
</script>
<style scoped>
</style>
