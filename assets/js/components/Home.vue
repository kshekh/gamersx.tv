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
      <component :is="defaultSceleton"></component>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";
import LazyLoadComponent from './LazyLoad';


export default {
  components: {
    ClassicSmSkeleton: ClassicSmSkeleton,

    FullWidthDescriptive: LazyLoadComponent({
      componentFactory: () => import('./front/FullWidthDescriptive.vue'),
      loading: ClassicSmSkeleton,
    }),

    Parallax: LazyLoadComponent({
      componentFactory: () => import('./front/Parallax.vue'),
      loading: ParallaxSkeleton,
    }),

    NumberedRow: LazyLoadComponent({
      componentFactory: () => import('./front/NumberedRow.vue'),
      loading: NumberedRowSkeleton,
    }),

    ClassicSm: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicSm.vue'),
      loading: ClassicSmSkeleton,
      loadingData: 200
    }),

    FullWidthImagery: LazyLoadComponent({
      componentFactory: () => import('./front/FullWidthImagery.vue'),
      loading: FullWidthImagerySkeleton,
    }),

    ClassicVertical: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicVertical.vue'),
      loading: ClassicVerticalSkeleton,
    }),

    ClassicMd: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicMd.vue'),
      loading: ClassicMdSkeleton,
    }),
    ClassicLg: LazyLoadComponent({
      componentFactory: () => import('./front/ClassicLg.vue'),
      loading: ClassicLgSkeleton,
    }),
  },
  data: function () {
    return {
      settings: {
        rows: []
      },
      defaultSceleton: "ClassicSmSkeleton",
      pollingApiData: null,
      requestPollingDelay: 90000};
  },
  methods: {
    requestHomeApi: function () {
      axios.get("/home/api")
        .catch(e => console.error(e))
        .then(response => {
          this.settings = response.data.settings;
        });
    }
  },
  mounted: function () {
    this.requestHomeApi();
    this.pollingApiData = window.setInterval(() => {
      this.requestHomeApi();
    }, this.requestPollingDelay);
  },
  destroyed: function () {
    window.clearInterval(this.pollingApiData);
  }
};

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function (n) {
  return ((this % n) + n) % n;
};
</script>
