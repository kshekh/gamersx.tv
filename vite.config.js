import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vuePlugin from "@vitejs/plugin-vue";
import { dirname, resolve } from 'path';
import { fileURLToPath } from 'url';

/* if you're using React */
// import react from '@vitejs/plugin-react';

const mainDir = dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [
        vuePlugin(),
        symfonyPlugin({
          stimulus: true
        }),
    ],
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                app: "./assets/app.js",
                main: "./assets/main.js",
                game: "./assets/js/game.js",
                home: "./assets/js/home.js",
                streamer: "./assets/js/streamer.js",
                streamerSelector: "./assets/js/streamerSelector.js",
                topicSelector: "./assets/js/topicSelector.js"
            },
        }
    },
    resolve: {
      alias: {
        vue: '@vue/compat',
        '~': resolve(mainDir, 'assets'),
      }
    },
});
