<template>
  <!-- remove "text-white" later -->
  <div class="text-white py-4 md:py-7">
    <template v-if="settings.rows.length">
      <div v-for="(row, index) in settings.rows" :key="row.id">
        <component
          :is="row.componentName"
          :settings="row"
          :rowPosition="index"
        ></component>
      </div>
    </template>
    <div v-else>
      <div v-for="(skeletonName, index) of skeletonsArray" :key="index">
        <component :is="skeletonName"></component>
      </div>
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

import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthDescriptiveSkeleton from "./skeletons/FullWidthDescriptiveSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";

export default {
  components: {
    ClassicSm: ClassicSm,
    ClassicMd: ClassicMd,
    ClassicLg: ClassicLg,
    ClassicVertical: ClassicVertical,
    FullWidthDescriptive: FullWidthDescriptive,
    FullWidthImagery: FullWidthImagery,
    Parallax: Parallax,
    NumberedRow: NumberedRow,
    // Skeletons
    ClassicSmSkeleton: ClassicSmSkeleton,
    ClassicMdSkeleton: ClassicMdSkeleton,
    ClassicLgSkeleton: ClassicLgSkeleton,
    ClassicVerticalSkeleton: ClassicVerticalSkeleton,
    NumberedRowSkeleton: NumberedRowSkeleton,
    ParallaxSkeleton: ParallaxSkeleton,
    FullWidthDescriptiveSkeleton: FullWidthDescriptiveSkeleton,
    FullWidthImagerySkeleton: FullWidthImagerySkeleton,
  },
  data: function() {
    return {
      settings: {
        rows: []
      },
      skeletonsArray: [
        "ClassicSmSkeleton",
        "ClassicMdSkeleton",
        "ClassicLgSkeleton",
        "ClassicVerticalSkeleton",
        "NumberedRowSkeleton",
        "ParallaxSkeleton",
        "FullWidthDescriptiveSkeleton",
        "FullWidthImagerySkeleton",
      ],
      pollingApiData: null,
      requestPollingDelay: 90000
    };
  },
  methods: {
    requestHomeApi: function() {
      axios.get("/home/api").then(response => {
        this.settings = response.data.settings;
      });
    }
  },
  mounted: function() {
    this.requestHomeApi();

    this.pollingApiData = setInterval(() => {
      this.requestHomeApi();
    }, this.requestPollingDelay);
  },
  beforeDestroy: function() {
    clearInterval(this.pollingApiData);
  }
};

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function(n) {
  return ((this % n) + n) % n;
};
</script>
