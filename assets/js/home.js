import { createPinia } from "pinia";
import { createApp } from "vue";
import Home from "./components/Home.vue";

const app = createApp(Home, {
  isVisibleVideoContainer: false,
  containerId: '',
  isPinnedContainer: false,
  isMoveContainer: false,
  embedRef: '',
})

app.use(createPinia());
app.mount('#app-home');