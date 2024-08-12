import { createApp } from "vue";
import { createPinia } from "pinia";
import Home from "./components/Home.vue";

const app = createApp({
  data() {
    return {
      isVisibleVideoContainer: false,
      containerId: "",
      isPinnedContainer: false,
      isMoveContainer: false,
      embedRef: "",
      currentVisibleContainerPositionY: "",
      currentVisibleContainerPositionX: "",
      startParentPosition: {
        Y: "",
        X: "",
      },
      bottomRightPosition: {
        Y: "",
        X: "",
      },
      pinnedPosition: {
        Y: "",
        X: "",
      },
      unPinnedPosition: {
        Y: "",
        X: "",
      },
      lastMovedPosition: {
        Y: "",
        X: "",
      },
      currentPosition: {
        Y: "",
        X: "",
      },
      positionBeforeUnpin: {
        top: "",
        left: "",
      },
      previousPinnedPosition: {
        y: "",
        x: "",
      },
    };
  },
});

app.use(createPinia());
app.component("Home", Home);
app.mount("#app-home");
