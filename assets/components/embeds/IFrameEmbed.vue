<template>
	<div class="w-auto p-4 bg-indigo-500 rounded-md">
		<iframe
			:src="'https://player.twitch.tv/?channel=' + name + '&parent=localhost&muted=true'"
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
		'name': {
			required: true,
			type: String
		}
	}
}
</script>

<style scoped>
</style>
