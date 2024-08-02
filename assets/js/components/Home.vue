<template>
  <!-- Root div with temporary text-white class -->
  <div class="text-white">
    <!-- Check if settings.rows is defined and has elements -->
    <template v-if="settings.rows && settings.rows.length">
      <!-- Loop through each row in settings.rows -->
      <div v-for="(row, index) in settings.rows" :key="row.id" :style="{paddingTop: row.rowPaddingTop + 'px', paddingBottom: row.rowPaddingBottom + 'px'}">
        <!-- Dynamically load and render the component specified in row.componentName -->
        <component :is="row.componentName" :settings="row" :rowPosition="index"></component>
      </div>
    </template>
    <div v-else>
      <!-- Check if cachedSkeletonRows has elements -->
      <template v-if="cachedSkeletonRows.length">
        <div>
          <!-- Loop through each row in cachedSkeletonRows -->
          <div v-for="(row, index) in cachedSkeletonRows" :key="index">
            <!-- Dynamically load and render the skeleton component for each row -->
            <component :is="row + 'Skeleton'" :rowPosition="index"></component>
          </div>
        </div>
      </template>
      <template v-else>
        <div>
          <!-- Loop through each row in defaultSkeletonRows if no cached skeletons are available -->
          <div v-for="(row, index) in defaultSkeletonRows" :key="index">
            <!-- Render the default skeleton component for each row -->
            <component :is="row" :rowPosition="index"></component>
          </div>
        </div>
      </template>
    </div>
    <!-- Modal component for user interaction -->
    <Modal v-model="modal" @update:modelValue="handleModalUpdate" no-close-on-backdrop ok-title="Continue anyway" @ok="handleCloseModal">
      <div class="flex flex-col md:flex-row items-center justify-start">
        <div class="w-full md:w-1/3 flex items-center justify-center md:justify-start">
          <!-- Video within the modal -->
          <video autoplay="autoplay" muted="muted" loop="loop" playsinline="" class="h-full md:w-full object-cover">
            <source src="https://gamersx-dev-dev-us-west-1-storage.s3.us-west-1.amazonaws.com/Experience+Popup+1+Iteration+(720).mp4" type="video/mp4">
          </video>
        </div>
        <div class="flex-grow flex flex-col items-center justify-center md:justify-start xs:p-4 md:p-0 md:m-2">
          <div class="text-xl xl:text-2xl text-center md:text-left xs:mb-2 sm:mb-2 md:mb-4 lg:mb-6">
            Skip the breaks* when you login with Twitch
          </div>
          <div class="flex justify-center space-x-1 sm:space-x-4 md:justify-start xs:mt-2 sm:mt-6 md:mt-4 lg:mt-6">
            <!-- Button to watch with breaks -->
            <button @click="handleCloseModal" class="elementor-button-x text-xxs xs:text-xxs sm:text-xs md:text-sm lg:text-sm xl:text-xl xxl:text-lg mx-2 sm:py-0 h-7 sm:h-10 xxs:px-0 xs:px-1 md:px-6">
              <span class="elementor-button-text">Watch With Breaks</span>
            </button>
            <!-- Button to login with Twitch -->
            <a href="api/twitch-login" @click="handleTwitchLogin" role="button" onmouseover="changeBtnColor(event)" onmouseout="changeNormalBtnColor(event)" class="flex items-center elementor-button text-xxs xs:text-xxs sm:text-xs md:text-sm lg:text-sm xl:text-xl xxl:text-lg mx-2 sm:py-0 h-7 sm:h-10 xxs:px-0 xs:-x-1 md:px-6">
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
    FullWidthDescriptiveSkeleton,
    ClassicSmSkeleton,
    ClassicMdSkeleton,
    ClassicLgSkeleton,
    NumberedRowSkeleton,
    ParallaxSkeleton,
    ClassicVerticalSkeleton,
    FullWidthImagerySkeleton,
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
      modal: false, // State for modal visibility
      settings: {
        rows: [] // Settings for the rows
      },
      defaultSkeleton: "FullWidthDescriptiveSkeleton", // Default skeleton component
      cachedSkeletonRows: [], // Cached skeleton rows from API
      defaultSkeletonRows: [
        "FullWidthDescriptiveSkeleton",
        "ClassicSmSkeleton",
        "NumberedRowSkeleton",
        "ClassicMdSkeleton",
        "ParallaxSkeleton",
        "ClassicLgSkeleton",
        "ClassicVerticalSkeleton",
        "FullWidthImagerySkeleton"
      ], // List of default skeleton components
      pollingApiData: null, // Interval for polling API data
      requestPollingDelay: 90000 // Delay for polling API requests
    };
  },
  methods: {
    // Request cached rows from API
    requestHomeCachedRowsApi() {
      return axios
        .get("/home/rows/api")
        .catch(e => console.error(e))
        .then(response => {
          if (response.data.settings.rows.length)
            this.cachedSkeletonRows = response.data.settings.rows;
        });
    },
    // Request home settings from API
    requestHomeApi() {
      axios
        .get("/home/api")
        .catch(e => console.error(e))
        .then(response => {
          this.settings = response.data.settings;
        });
    },
    // Request session information from API
    requestSessionsApi() {
      return new Promise((resolve, reject) => {
        if (!Cookies.get("twitch_")) {
          axios.get("/home/sessions/api")
            .then(response => {
              if (response.data.isLoggedIn && !response.data.isRequiredToLoginTwitch) {
                this.modal = false;
              } else {
                this.$nextTick(() => {
                  this.modal = false; // Disable the modal during development
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
    // Handle modal update event
    handleModalUpdate(val) {
      if (!val) {
        setTimeout(() => {
          this.modal = false;
        }, 500);
      } else {
        this.modal = true;
      }
    },
    // Handle closing the modal
    handleCloseModal() {
      Cookies.set("twitch_", "demo", { expires: 1 });
      this.modal = false;
      this.handleModalUpdate(false);
    },
    // Handle Twitch login
    handleTwitchLogin() {
      Cookies.set("twitch_", "demo", { expires: 1 });
      this.modal = false;
      this.handleModalUpdate(false);
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