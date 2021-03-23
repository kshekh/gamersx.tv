<template>
  <div class="container w-screen text-center mx-auto">
    <div class="container flex-col">
      <div
        v-for="row in settings.rows">
        <home-row
          v-bind:settings="row"
        ></home-row>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import HomeRow from './layout/HomeRow.vue';

export default {
  components: {
    'home-row': HomeRow
  },
  data: function()  {
    return {
      settings: {
        rows: []
      },
    }
  },
  mounted: function() {
    axios
      .get('/home/api')
      .then(response => {
        this.settings = response.data.settings;
      });
  }
}

/** We use this a lot for scrolling because JS % is remainder, not modulo **/
Number.prototype.mod = function(n) {
  return ((this % n) + n) % n;
}
</script>
<style scoped>
</style>
