<template>
  <!-- remove "text-white" later -->
  <div class="text-white py-4 md:py-7">
    <template v-if="settings.rows && settings.rows.length">
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
            <component :is="row + 'Skeleton'" :rowPosition="index"></component>
          </div>
        </div>
      </template>
      <template v-else>
        <div>
          <div v-for="(row, index) in defaultSkeletonRows" :key="index">
            <component :is="row" :rowPosition="index"></component>
          </div>
        </div>
      </template>
      <!--      <component v-else :is="defaultSkeleton"></component>-->
    </div>
    <Modal
      v-model="modal"
      no-close-on-backdrop
      ok-title="Continue anyway"
      @ok="handleCloseModal"
    >
      <div class="text-center p-0 md:p-5">
        <div class="mb-4 text-grey-400 text-lg mx-auto max-w-[300px]">
          Sign into
          <span class="">
            <span
              target="_blank"
              rel="noopener noreferrer"
              class="text-purple underline"
              @click="handleLogin"
            >
              twitch.tv
            </span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 15 15"
              class=" inline-block"
            >
              <path
                fill="currentColor"
                fill-rule="evenodd"
                d="M3 2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h9a1 1 0 0 0 1-1V8.5a.5.5 0 0 0-1 0V12H3V3h3.5a.5.5 0 0 0 0-1H3Zm9.854.146a.5.5 0 0 1 .146.351V5.5a.5.5 0 0 1-1 0V3.707L6.854 8.854a.5.5 0 1 1-.708-.708L11.293 3H9.5a.5.5 0 0 1 0-1h3a.499.499 0 0 1 .354.146Z"
                clip-rule="evenodd"
              />
            </svg>
          </span>
          for best viewing experience
        </div>
        <div class="mb-4">
          <video autoplay muted loop width="100%" playsinline>
            <source
              src="https://gamersx-dev-dev-us-west-1-storage.s3.us-west-1.amazonaws.com/Experience+Popup+1+Iteration+(720).mp4"
              type="video/mp4"
            />
          </video>
        </div>
        <p>
          An active session in the background will avoid interruptions..
        </p>
      </div>
    </Modal>
  </div>
</template>
<script>
import axios from "axios";
import LazyLoadComponent from "./LazyLoad";
import FullWidthDescriptiveSkeleton from "./skeletons/FullWidthDescriptiveSkeleton";
import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";
import Modal from "./Modal";
import Cookies from "js-cookie";
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
    Modal,
    FullWidthDescriptive: LazyLoadComponent({
      componentFactory: () => import("./front/FullWidthDescriptive.vue"),
      loading: FullWidthDescriptiveSkeleton
    }),
    Parallax: LazyLoadComponent({
      componentFactory: () => import("./front/Parallax.vue"),
      loading: ParallaxSkeleton
    }),
    NumberedRow: LazyLoadComponent({
      componentFactory: () => import("./front/NumberedRow.vue"),
      loading: NumberedRowSkeleton
    }),
    ClassicSm: LazyLoadComponent({
      componentFactory: () => import("./front/ClassicSm.vue"),
      loading: ClassicSmSkeleton,
      loadingData: 200
    }),
    FullWidthImagery: LazyLoadComponent({
      componentFactory: () => import("./front/FullWidthImagery.vue"),
      loading: FullWidthImagerySkeleton
    }),
    ClassicVertical: LazyLoadComponent({
      componentFactory: () => import("./front/ClassicVertical.vue"),
      loading: ClassicVerticalSkeleton
    }),
    ClassicMd: LazyLoadComponent({
      componentFactory: () => import("./front/ClassicMd.vue"),
      loading: ClassicMdSkeleton
    }),
    ClassicLg: LazyLoadComponent({
      componentFactory: () => import("./front/ClassicLg.vue"),
      loading: ClassicLgSkeleton
    })
  },
  data: function() {
    return {
      modal: false,
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
        "FullWidthImagerySkeleton"
      ],
      pollingApiData: null,
      requestPollingDelay: 90000
    };
  },
  methods: {
    requestHomeCachedRowsApi() {
      return axios
        .get("/home/rows/api")
        .catch(e => console.error(e))
        .then(response => {
          // this.settings.rows = [];
          if (response.data.settings.rows.length)
            this.cachedSkeletonRows = response.data.settings.rows;
        });
    },
    requestHomeApi() {
      axios
        .get("/home/api")
        .catch(e => console.error(e))
        .then(response => {
          this.settings = response.data.settings;
        });
    },
    requestSessionsApi: function () {
      axios.get("/home/sessions/api")
        .catch(e => console.error(e))
        .then(response => {
          if (response.data.isLoggedIn && !response.data.isRequiredToLoginTwitch) {
            this.modal = false;
          } else {
            this.modal = true;
          }
        });
    },
    handleCloseModal() {
      // 24 hours
      Cookies.set("twitch_", "demo", { expires: 1 });
      this.modal = false;
    },
    handleLogin() {
      // 24 hours
      Cookies.set("twitch_", "demo", { expires: 1 });
      window.open("https://twitch.tv/login", "_blank");
    },
    requestSessionsApi: function () {
      const cookie = Cookies.get("twitch_");
      if (cookie) return
      axios.get("/home/sessions/api")
        .catch(e => console.error(e))
        .then(response => {
          if (response.data.isLoggedIn && !response.data.isRequiredToLoginTwitch) {
            this.modal = false;
          } else {
            this.modal = true;
          }
        });
    },
  },
  mounted: function() {
    this.requestSessionsApi();
    this.requestHomeCachedRowsApi().then(()=>{
      this.requestHomeApi();
    });
    this.pollingApiData = window.setInterval(() => {
      this.requestHomeApi();
    }, this.requestPollingDelay);
  },
  destroyed: function() {
    window.clearInterval(this.pollingApiData);
  }
};
/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function(n) {
  return ((this % n) + n) % n;
};
</script>