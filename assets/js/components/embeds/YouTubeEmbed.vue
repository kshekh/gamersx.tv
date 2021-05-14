<template>
	<div class="w-auto p-4">
    <div :id="embedData.elementId"></div>
	</div>
</template>

<script>
export default {
	name: 'YouTubeEmbed',
	props: {
    embedData: Object,
    elementId: String,
  },
  data: function() {
    return {
      height: 300,
      width: 540,
      embed: {},
    }
  },
  mounted: function() {
    this.embed = new YT.Player(this.embedData.elementId, {
      width: this.width,
      height: this.height,
      videoId: this.embedData.video,
      autoplay: false,
    });
  }
}
</script>

<style scoped>
</style>
