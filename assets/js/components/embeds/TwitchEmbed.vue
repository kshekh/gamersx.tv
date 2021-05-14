<template>
	<div class="w-auto p-4">
    <div :id="playerId"></div>
	</div>
</template>

<script>
export default {
	name: 'TwitchEmbed',
	props: {
		'channel': {
			type: String
    },
    'video': {
			type: String
    },
    'elementId': {
      type: String
    }
  },
  computed: {
    playerId: function() {
      return this.elementId ? this.elementId : 'twitch-embed-' + (this.channel ?? this.video);
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
      video: this.video,
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
