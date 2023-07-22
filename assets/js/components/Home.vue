<template>
  <!-- remove "text-white" later -->
  <div class="text-white">
    <template v-if="settings.rows && settings.rows.length">
      <div v-for="(row, index) in settings.rows" :key="row.id">
        <component :is="row.componentName" :settings="row" :rowPosition="index"></component>
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
    <Modal v-model="modal" no-close-on-backdrop ok-title="Continue anyway" @ok="handleCloseModal">
      <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:items-start md:justify-between">
        <div class="w-full md:w-1/3 mb-3 md:mb-0 md:mr-5">
          <video autoplay muted loop playsinline class="h-full w-full md:max-w-sm object-cover">
            <source
              src="https://gamersx-dev-dev-us-west-1-storage.s3.us-west-1.amazonaws.com/Experience+Popup+1+Iteration+(720).mp4"
              type="video/mp4" />
          </video>
        </div>
        <div class="w-full md:w-2/3 space-y-4">
          <div class="p-1"></div>
          <div class="text-lg">
            <!--Sign into
            <span target="_blank" rel="noopener noreferrer" class="text-purple underline"
              @click="handleLogin">twitch.tv</span> for best viewing experience-->
            <!--Enjoy uninterrupted live streams when you log in with Twitch-->
            <!--Skip ad breaks and enjoy non-stop streaming by logging in with Twitch Turbo.-->
            Skip the breaks* when you login with Twitch
          </div>
          <br>
          <br>
          <!-- "Continue anyway" button placed directly below the text -->
          <div class="flex">
            <button class="elementor-button-x" @click="handleCloseModal">
              <span class="elementor-button-text">Watch With Breaks</span>
            </button>
            <a href="api/twitch-login" @click="handleTwitchLogin"
              class="flex items-center elementor-button text-[5px] sm:text-base py-1 px-2 sm:py-0 sm:px-[40px] h-7 sm:h-10"
              role="button" onmouseover="changeBtnColor(event)" onmouseout="changeNormalBtnColor(event)">
              <span class="elementor-button-text">Login With Twitch</span>
              <img src="/images/twitch-icon-white.png" class="ml-2 w-2.5 h-2.5 sm:w-5 sm:h-5 twitch-btn-icon">
            </a>
          </div>
        </div>
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
  data: function () {
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
    requestSessionsApi() {
      return new Promise((resolve, reject) => {
        if (!Cookies.get("twitch_")) {
          axios.get("/home/sessions/api")
            .then(response => {
              if (response.data.isLoggedIn && !response.data.isRequiredToLoginTwitch) {
                this.modal = false;
              } else {
                // Immediately after setting `modal` to `true`, request Vue to wait for next DOM update cycle
                this.$nextTick(() => {
                  this.modal = true;
                });
              }
              resolve(response);
            })
            .catch(error => {
              console.error(error);
              reject(error);
            })
        } else {
          resolve();
        }
      });
    },
    handleCloseModal() {
      // 24 hours
      Cookies.set("twitch_", "demo", { expires: 1 });
      this.modal = false;
    },
    handleTwitchLogin() {
      // 24 hours
      Cookies.set("twitch_", "demo", { expires: 1 });
      this.modal = false;
    },
  },
  mounted: function () {
    this.requestSessionsApi();
    this.requestHomeCachedRowsApi().then(() => {
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