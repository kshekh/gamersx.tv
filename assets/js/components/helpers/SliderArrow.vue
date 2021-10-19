<template>
  <button
    @click="handleClick()"
    class="transition-all duration-300 transform hover:scale-110"
    :class="[
      videoTypeClass,
      {
        'transform rotate-180': !isNext
      }
    ]"
  >
    <svg
      class="w-4 h-4 md:w-8 md:h-8 fill-current"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 512 512"
    >
      <path
        d="m506.134 241.843-.018-.019-104.504-104c-7.829-7.791-20.492-7.762-28.285.068-7.792 7.829-7.762 20.492.067 28.284L443.558 236H20c-11.046 0-20 8.954-20 20s8.954 20 20 20h423.557l-70.162 69.824c-7.829 7.792-7.859 20.455-.067 28.284 7.793 7.831 20.457 7.858 28.285.068l104.504-104 .018-.019c7.833-7.818 7.808-20.522-.001-28.314z"
      />
    </svg>
  </button>
</template>

<script>
export default {
  name: "SliderArrow",
  props: {
    videoType: {
      type: String,
      required: true
    },
    isNext: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    videoTypeClass() {
      let className;

      switch (this.videoType) {
        case "youtube":
          className = "text-red";
          break;
        case "twitch":
          className = "text-purple";
          break;
      }

      return className;
    }
  },
  methods: {
    handleClick() {
      this.$emit("arrow-clicked");
    }
  }
};
</script>
