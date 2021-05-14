<template>
	<div class="w-auto p-4">
    <div :id="embedData.elementId"></div>
	</div>
</template>

<script>
export default {
	name: 'TwitchEmbed',
	props: {
    embedData: Object,
  },
  data: function() {
    return {
      height: 300,
      width: 540,
      embed: {},
    }
  },
  mounted: function() {
    this.embed = new Twitch.Embed(this.embedData.elementId, {
      width: this.width,
      height: this.height,
      channel: this.embedData.channel,
      video: this.embedData.video,
      layout: 'video',
      autoplay: false,
      muted: true,
      parent: window.location.hostname
    });
  }
}
</script>

<style scoped>
</style>
