<template>
	<div class="w-auto p-4">
    <div :id="playerId"></div>
	</div>
</template>

<script>
export default {
	name: 'JsEmbed',
	props: {
		'channel': {
			required: true,
			type: String
		}
  },
  computed: {
    playerId: function() {
      return 'twitch-embed-' + this.channel;
    }
  },
  data: function() {
    return {
      height: 300,
      width: 540,
      embed: {},
    }
  },
  mounted: function() {
    this.embed = new Twitch.Embed(this.playerId, {
      width: this.width,
      height: this.height,
      channel: this.channel,
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
