<template>
	<div :class="imageClass" class="p-4" >
    <img :width="width" :height="height" :src="sizedUrl">
  </div>
</template>
<script>

export default {
  name: 'TwitchArt',
  props: [ 'src', 'imageType'],
  computed: {
    width: function() {
      if (this.imageType === 'profile') {
        return 300;
      } else if (this.imageType === 'boxArt') {
        return 225;
      }
    },
    height: function() {
      return 300;
    },
    sizedUrl: function() {
      return this.src.replace('{width}', this.width).replace('{height}', this.height);
    },
    imageClass: function() {
      if (this.imageType === 'profile') {
        return 'profile-pic';
      } else if (this.imageType === 'boxArt') {
        return 'box-art';
      }
    }
  }
}
</script>
<style scoped>
</style>
