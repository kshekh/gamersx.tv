<template>
  <button
    class="w-8 h-8 md:h-10 md:w-10 xl:w-12 xl:h-12 flex cursor-pointer rounded-full"
    :class="[videoTypeClass]"
  >
    <svg
      class="text-white fill-current w-4 h-4 m-auto"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 50 50"
    >
      <path
        d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 37.683594 12.316406 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 19 35 L 19 15 L 36 25 Z"
      ></path>
    </svg>
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
