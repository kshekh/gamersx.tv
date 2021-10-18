<template>
  <button class="flex p-1" @click="handleClick">
    <span
      class="w-1 h-1 rounded-full m-auto md:w-2 md:h-2 xl:w-3 xl:h-3 transition-all"
      :class="{
        'w-3 md:w-4 xl:w-8': isDotActive,
        'bg-grey': !isDotActive,
        'bg-purple': isDotActive && embedType === 'TwitchEmbed',
        'bg-red': isDotActive && embedType === 'YoutubeEmbed'
      }"
    ></span>
  </button>
</template>

<script>
export default {
  name: "SliderDot",
  props: {
    dotIndex: {
      type: Number,
      required: true
    },
    embedType: {
      type: String,
      default: "TwitchEmbed"
    },
    isDotActive: {
      type: Boolean,
      required: true
    }
  },
  methods: {
    handleClick() {
      this.$emit("slider-dot-clicked", this.dotIndex);
    }
  }
};
</script>
