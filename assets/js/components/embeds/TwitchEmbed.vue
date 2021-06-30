<template>
	<div class="w-auto p-4">
    <img v-if="hasOverlay" alt="Embed's Custom Overlay" :src="embedData.overlay">
    <div :id="embedData.elementId" v-bind:class="{ hidden: hasOverlay }"></div>
	</div>
</template>

<script>
export default {
	name: 'TwitchEmbed',
	props: {
    embedData: Object,
  },
  computed: {
    hasOverlay: function() {
      return this.embedData.overlay;
    },
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
