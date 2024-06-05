import { createApp } from "vue";
import VueSource from "vue-source";
import Game from "./components/Game.vue";

/**
 * * Create a fresh Vue Application instance
 * */
const app = createApp(Game);

app.use(VueSource);
app.mount("#app-game");
