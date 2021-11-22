<template>
  <button class="flex transition-all duration-300 transform hover:scale-110" @click="handleClick">
    <span
      class="rounded-full m-auto h-1.5 md:h-2 xl:h-3 transition-all"
      :class="{
        'w-3 md:w-4 xl:w-8': isDotActive,
        'bg-grey w-1.5 md:w-2 xl:w-3': !isDotActive,
        'bg-purple': isDotActive && embedType === 'TwitchEmbed',
        'bg-red': isDotActive && embedType === 'YouTubeEmbed'
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
