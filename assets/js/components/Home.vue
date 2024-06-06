<template>
  <!-- remove "text-white" later -->
  <div class="text-white">
    <template v-if="settings.rows && settings.rows.length">
      <div v-for="(row, index) in settings.rows" :key="row['id']" :style="{paddingTop:row['rowPaddingTop']+'px',paddingBottom:row['rowPaddingBottom']+'px'}">
        <component :is="row['componentName']" :settings="row" :rowPosition="index"></component>
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
    <Modal v-model="modal" @update:modelValue="handleModalUpdate" no-close-on-backdrop ok-title="Continue anyway"
      @ok="handleCloseModal" value>
      <div class="flex flex-col md:flex-row items-center justify-start">
        <div class="w-full md:w-1/3 flex items-center justify-center md:justify-start">
          <video autoplay="autoplay" muted="muted" loop="loop" playsinline="" class="h-full md:w-full object-cover">
            <source
              src="https://gamersx-dev-dev-us-west-1-storage.s3.us-west-1.amazonaws.com/Experience+Popup+1+Iteration+(720).mp4"
              type="video/mp4">
          </video>
        </div>
        <div class="flex-grow flex flex-col items-center justify-center md:justify-start xs:p-4 md:p-0 md:m-2">
          <div class="text-xl xl:text-2xl text-center md:text-left xs:mb-2 sm:mb-2 md:mb-4 lg:mb-6">
            Skip the breaks* when you login with Twitch
          </div>
          <div class="flex justify-center space-x-1 sm:space-x-4 md:justify-start xs:mt-2 sm:mt-6 md:mt-4 lg:mt-6">
            <button @click="handleCloseModal"
              class="elementor-button-x text-xxs xs:text-xxs sm:text-xs md:text-sm lg:text-sm xl:text-xl xxl:text-lg mx-2 sm:py-0 h-7 sm:h-10 xxs:px-0 xs:px-1 md:px-6">
              <span class="elementor-button-text">Watch With Breaks</span>
            </button>
            <a href="api/twitch-login" @click="handleTwitchLogin" role="button" onmouseover="changeBtnColor(event)"
              onmouseout="changeNormalBtnColor(event)"
              class="flex items-center elementor-button text-xxs xs:text-xxs sm:text-xs md:text-sm lg:text-sm xl:text-xl xxl:text-lg mx-2 sm:py-0 h-7 sm:h-10 xxs:px-0 xs:-x-1 md:px-6">
              <span class="elementor-button-text">Login With Twitch</span>
              <img :src="TwitchIconWhite" class="ml-2 w-2.5 h-2.5 sm:w-5 sm:h-5 twitch-btn-icon">
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
import FullWidthDescriptiveSkeleton from "./skeletons/FullWidthDescriptiveSkeleton.vue";
import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";
import Modal from "./Modal.vue";
import Cookies from "js-cookie";
import TwitchIconWhite from "~/images/twitch-icon-white.png"

export default {
  components: {
    TwitchIconWhite: TwitchIconWhite,
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
};
</script>

<script setup>
import axios from "axios";
import FullWidthDescriptiveSkeleton from "./skeletons/FullWidthDescriptiveSkeleton.vue";
import ClassicSmSkeleton from "./skeletons/ClassicSmSkeleton.vue";
import ClassicMdSkeleton from "./skeletons/ClassicMdSkeleton.vue";
import ClassicLgSkeleton from "./skeletons/ClassicLgSkeleton.vue";
import ClassicVerticalSkeleton from "./skeletons/ClassicVerticalSkeleton.vue";
import NumberedRowSkeleton from "./skeletons/NumberedRowSkeleton.vue";
import ParallaxSkeleton from "./skeletons/ParallaxSkeleton.vue";
import FullWidthImagerySkeleton from "./skeletons/FullWidthImagerySkeleton.vue";
import Modal from "./Modal.vue";
import Cookies from "js-cookie";
import TwitchIconWhite from "~/images/twitch-icon-white.png"
import {ref, onMounted, onUnmounted, defineAsyncComponent, nextTick} from "@vue/compat";

// const FullWidthDescriptive = defineAsyncComponent({
//   loader: () => import("./front/FullWidthDescriptive.vue"),
//   loadingComponent: FullWidthDescriptiveSkeleton
// });
// const Parallax = defineAsyncComponent({
//   loader: () => import("./front/Parallax.vue"),
//   loadingComponent: ParallaxSkeleton
// });
// const NumberedRow = defineAsyncComponent({
//   loader: () => import("./front/NumberedRow.vue"),
//   loadingComponent: NumberedRowSkeleton
// });
// const ClassicSm = defineAsyncComponent({
//   loader: () => import("./front/ClassicSm.vue"),
//   loadingComponent: ClassicSmSkeleton,
//   delay: 200
// });
// const ClassicMd = defineAsyncComponent({
//   loader: () => import("./front/ClassicMd.vue"),
//   loadingComponent: ClassicMdSkeleton
// });
// const ClassicLg = defineAsyncComponent({
//   loader: () => import("./front/ClassicLg.vue"),
//   loadingComponent: ClassicLgSkeleton
// });
// const FullWidthImagery = defineAsyncComponent({
//   loader: () => import("./front/FullWidthImagery.vue"),
//   loadingComponent: FullWidthImagerySkeleton
// });
// const ClassicVertical = defineAsyncComponent({
//   loader: () => import("./front/ClassicVertical.vue"),
//   loadingComponent: ClassicVerticalSkeleton
// });

const cachedSkeletonRows = ref([]);
const defaultSkeleton = "FullWidthDescriptiveSkeleton";
const defaultSkeletonRows = [
  "FullWidthDescriptiveSkeleton",
  "ClassicSmSkeleton",
  "NumberedRowSkeleton",
  "ClassicMdSkeleton",
  "ParallaxSkeleton",
  "ClassicLgSkeleton",
  "ClassicVerticalSkeleton",
  "FullWidthImagerySkeleton"
];
const modal = ref(false);
const pollingApiData = ref(null);
const settings = ref({
  rows: []
});
const requestPollingDelay = 90000;

async function requestHomeCachedRowsApi() {
  return axios
    .get("/home/rows/api")
    .catch(e => console.error(e))
    .then(response => {
      // this.settings.rows = [];
      if (response.data.settings.rows.length)
        cachedSkeletonRows.value = response.data.settings.rows;
    });
}
function requestHomeApi() {
  axios
    .get("/home/api")
    .catch(e => console.error(e))
    .then(response => {
      console.log(response.data.settings);
      settings.value = response.data.settings;
    });
}

function requestSessionsApi() {
  return new Promise((resolve, reject) => {
    if (!Cookies.get("twitch_")) {
      axios.get("/home/sessions/api")
        .then(response => {
          if (response.data.isLoggedIn && !response.data.isRequiredToLoginTwitch) {
            modal.value = false;
          } else {
            // Immediately after setting `modal` to `true`, request Vue to wait for next DOM update cycle
            nextTick(() => {
              modal.value = false; //false here will disable the modal entirely, helpful to disable when developing
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
}

/*
* Handlers
*/
function handleCloseModal() {
  // 24 hours
  Cookies.set("twitch_", "demo", { expires: 1 });
  modal.value = false;
  handleModalUpdate(false);
}
function handleModalUpdate(val) {
  // This condition avoids abruptly removing DOM in Modal when directly changing v-model value.
  if (!val) {
    setTimeout(() => {
      modal.value = false;
    }, 500);
  } else {
    modal.value = true;
  }
}
function handleTwitchLogin() {
  // 24 hours
  Cookies.set("twitch_", "demo", { expires: 1 });
  modal.value = false;
  handleModalUpdate(false);
}

onMounted(() => {
  requestSessionsApi();
  requestHomeCachedRowsApi().then(() => {
    requestHomeApi();
  });
  pollingApiData.value = window.setInterval(() => {
    requestHomeApi();
  }, requestPollingDelay);
});
onUnmounted(() => {
  window.clearInterval(pollingApiData);
});

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function (n) {
  return ((this % n) + n) % n;
};
</script>