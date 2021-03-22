<template>
	<div class="w-auto p-4 bg-indigo-500 rounded-md">
		<iframe
      :src="playerUrl"
			height="300"
			width="540"
			allowfullscreen="false">
		</iframe>
	</div>
</template>

<script>
export default {
	name: 'IFrameEmbed',
	props: {
		'channel': {
			required: true,
			type: String
		}
  },
  computed: {
    playerUrl: function() {
      return 'https://player.twitch.tv/?channel=' + this.channel +
        '&parent=' + window.location.hostname +
        '&autoplay=false' +
        '&muted=true'
      ;
    }
  }
}
</script>

<style scoped>
</style>
