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
      <template v-if="cachedSkeletonRows.length">
        <div>
          <div v-for="(row, index) in cachedSkeletonRows" :key="index">
            <component
              :is="row+'Skeleton'"
              :rowPosition="index"
            ></component>
          </div>
        </div>
      </template>
      <template v-else>
        <div>
          <div v-for="(row, index) in defaultSkeletonRows" :key="index">
            <component
              :is="row"
              :rowPosition="index"
            ></component>
          </div>
        </div>
      </template>
<!--      <component v-else :is="defaultSkeleton"></component>-->
    </div>
  </div>
</template>
<script>
import axios from "axios";
import LazyLoadComponent from './LazyLoad';
import FullWidthDescriptiveSkeleton from "./skeletons/FullWidthDescriptiveSkeleton";
import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";


export default {
  components: {
    FullWidthDescriptiveSkeleton: FullWidthDescriptiveSkeleton,
    ClassicSmSkeleton: ClassicSmSkeleton,
    ClassicMdSkeleton: ClassicMdSkeleton,
    ClassicLgSkeleton: ClassicLgSkeleton,
    NumberedRowSkeleton: NumberedRowSkeleton,
    ParallaxSkeleton: ParallaxSkeleton,
    ClassicVerticalSkeleton: ClassicVerticalSkeleton,
    FullWidthImagerySkeleton: FullWidthImagerySkeleton,

    FullWidthDescriptive: LazyLoadComponent({
      componentFactory: () => import('./front/FullWidthDescriptive.vue'),
      loading: FullWidthDescriptiveSkeleton,
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
      defaultSkeleton: "FullWidthDescriptiveSkeleton",
      cachedSkeletonRows: [],
      defaultSkeletonRows: [
        "FullWidthDescriptiveSkeleton",
        "ClassicSmSkeleton",
        "NumberedRowSkeleton",
        "ClassicMdSkeleton",
        "ParallaxSkeleton",
        "ClassicLgSkeleton",
        "ClassicVerticalSkeleton",
        "FullWidthImagerySkeleton",
      ],
      pollingApiData: null,
      requestPollingDelay: 90000};
  },
  methods: {
    requestHomeCachedRowsApi: function () {
      return axios.get("/home/rows/api")
        .catch(e => console.error(e))
        .then(response => {
          this.settings.rows = [];
          if (response.data.settings.rows.length)
            this.cachedSkeletonRows = response.data.settings.rows;
        });
    },
    requestHomeApi: function () {
      axios.get("/home/api")
        .catch(e => console.error(e))
        .then(response => {
          this.settings = response.data.settings;
        });
    }
  },
  mounted: function () {
    this.requestHomeCachedRowsApi().then(()=>{
      this.requestHomeApi();
    });
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
