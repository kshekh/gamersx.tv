<template>
  <div class="container text-center mx-auto">
    <div class="container flex-col">
      <div class="w-16"
        v-for="row in settings.rows">
        <home-row
          v-bind:settings="row"
        ></home-row>
      </div>
    </div>
  </div>
</template>
<script>
import HomeRow from './layout/HomeRow.vue';

export default {
  components: {
    'home-row': HomeRow
  },
  data: function()  {
    return {
      settings: {},
    }
  },
  mounted: function() {
    this.settings = JSON.parse(this.$el.parentNode.dataset['settings']);
  }
}
</script>
<style scoped>
</style>
