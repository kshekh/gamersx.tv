import Vue from 'vue';
import StreamerSelector from './components/StreamerSelector.vue'
window.EventBus = new Vue();
/**
 * * Create a fresh Vue Application instance
 * */
new Vue({
    el: '#app-streamer-selector',
    components: {StreamerSelector}
});
