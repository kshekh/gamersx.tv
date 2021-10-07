<template>
  <button
    class="w-8 h-8 md:h-10 md:w-10 xl:w-12 xl:h-12 flex cursor-pointer rounded-full"
    :class="[videoTypeClass]"
  >
    <div class="m-auto pl-1">
      <svg class="text-white fill-current w-4 h-4 " viewBox="0 0 16 16">
        <path d="M12.876 8.311.62 16.131V.491l12.255 7.82Z" />
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
    }
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
