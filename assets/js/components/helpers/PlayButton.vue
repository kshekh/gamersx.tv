<template>
  <button
    class="flex cursor-pointer transition-all duration-300 rounded-full transform hover:scale-105"
    :class="[videoTypeClass, buttonClass]"
  >
    <div class="m-auto" :class="wrapperClass">
      <!-- <svg class="text-white fill-current" viewBox="0 0 16 16" :class="svgClass">
        <path d="M12.876 8.311.62 16.131V.491l12.255 7.82Z" />
      </svg> -->
      <!-- <svg class="text-white fill-current" viewBox="0 0 19 30" :class="svgClass">
        <path d="M 19 14.999 L 0 30 L 0 0 L 19 14.999 Z"/>
      </svg> -->
      <svg class="text-white fill-current" viewBox="0 0 21 28" fill="none" :class="svgClass">
        <path d="M 21 13.999 L 0 28 L 0 0 L 21 13.999 Z"/>
      </svg>
    </div>
  </button>
</template>

<script>
export default {
  name: "PlayButton",
  props: {
    videoType: {
      type: String,
      required: true
    },
    svgClass: {
      type: String,
      default: "w-4"
    },
    wrapperClass: {
      type: String,
      default: "pl-1"
    },
    buttonClass: {
      type: String,
      default: "w-8 h-8 md:h-10 md:w-10 xl:w-12 xl:h-12",
    },
  },
  computed: {
    videoTypeClass() {
      let className;

      switch (this.videoType) {
        case "youtube":
          className = "bg-red shadow-youtube";
          break;
        case "twitch":
          className = "bg-purple shadow-twitch";
          break;
      }

      return className;
    }
  }
};
</script>
