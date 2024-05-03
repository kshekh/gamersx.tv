import Vue from 'vue';
import TopicSelector from './components/TopicSelector'
window.EventBus = new Vue();
/**
 * * Create a fresh Vue Application instance
 * */
new Vue({
    el: '#app-topic-selector',
    components: {TopicSelector}
});
