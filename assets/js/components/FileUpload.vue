<template>
  <div class="instructions">
    <p>Please use the "Browse" button to choose a file from your computer, then press "Upload" to send the file to the server. It will be validated and you should get a message telling you whether the change in settings was successful.</p>
    <form enctype="multipart/form-data" method="post" action="/settings/upload" >
      <input type="file" name="settings">
      <button class="btn btn-small btn-primary" type="submit">Upload</button>
    </form>
  </div>
</template>
<script>

export default {
  components: {
  },
  data: function()  {
    return {
      message: 'Welcome to the File Uploader',
    }
  },
  methods: {
    getSettings() {
      console.log('getting settings');
    }
  },
  mounted: function() {
    // this.info = JSON.parse(this.$el.parentNode.dataset['info']);
    // this.show('featured');
  }
}
</script>
<style scoped>
</style>
