<template>
  <!-- remowe "text-white" later -->
  <div class="text-white py-4 md:py-7">
    <div v-for="(row, index) in settings.rows" :key="row.id">
      <component :is="row.componentName" :settings="row" :rowPosition="index"></component>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import ClassicSm from "./front/ClassicSm.vue";
import ClassicMd from "./front/ClassicMd.vue";
import ClassicLg from "./front/ClassicLg.vue";
import ClassicVertical from "./front/ClassicVertical.vue";
import FullWidthDescriptive from "./front/FullWidthDescriptive.vue";
import FullWidthImagery from "./front/FullWidthImagery.vue";
import Parallax from "./front/Parallax.vue";
import NumberedRow from "./front/NumberedRow.vue";

export default {
  components: {
    ClassicSm: ClassicSm,
    ClassicMd: ClassicMd,
    ClassicLg: ClassicLg,
    ClassicVertical: ClassicVertical,
    FullWidthDescriptive: FullWidthDescriptive,
    FullWidthImagery: FullWidthImagery,
    Parallax: Parallax,
    NumberedRow: NumberedRow
  },
  data: function() {
    return {
      settings: {
        rows: []
      },
      pollingApiData: null,
      requestPollingDelay: 90000
    }
  },
  methods: {
    requestHomeApi: function() {
      axios.get("/home/api").then(response => {
        this.settings = response.data.settings;
      });
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
};

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function(n) {
  return ((this % n) + n) % n;
};
</script>
