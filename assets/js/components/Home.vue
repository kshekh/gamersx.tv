<template>
  <div class="container text-center mx-auto">
    <div class="container divide-y-4 divide-current my-4 flex-col">
      <div
        v-for="row in settings.rows">
        <component
          :is="row.componentName"
          v-bind:settings="row"
        ></component>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import HomeRow from './front/HomeRow.vue';
import CustomBgArtRow from './front/CustomBgArtRow.vue';
import NumberedRow from './front/NumberedRow.vue';

export default {
  components: {
    'HomeRow': HomeRow,
    'CustomBgArtRow': CustomBgArtRow,
    'NumberedRow': NumberedRow,
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
