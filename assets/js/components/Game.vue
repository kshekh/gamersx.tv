<template>
  <div class="container text-center mx-auto">
    <div class="container flex-col">
      <p>Welcome to the Game Page</p>
    </div>
  </div>
</template>
<script>

export default {
  components: {
    // 'home-row': HomeRow
  },
  data: function()  {
    return {
      settings: {},
    }
  },
  mounted: function() {
    // this.settings = JSON.parse(this.$el.parentNode.dataset['settings']);
  }
}
</script>
<style scoped>
</style>
