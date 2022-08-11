<template>

  <button

    @click="handleClick()"
    class="transition-all duration-300 transform hover:scale-110 overBG"
    :class="[
      videoTypeClass,
      {
        'transform rotate-180': !isNext
      }
    ]"
  >
<!--    <svg-->
<!--      style="width: 1.5rem"-->
<!--      class="w-4 h-4 md:w-8 md:h-8 fill-current"-->
<!--      xmlns="http://www.w3.org/2000/svg"-->
<!--      viewBox="0 0 512 512"-->
<!--    >-->
<!--      <path-->
<!--        d="m506.134 241.843-.018-.019-104.504-104c-7.829-7.791-20.492-7.762-28.285.068-7.792 7.829-7.762 20.492.067 28.284L443.558 236H20c-11.046 0-20 8.954-20 20s8.954 20 20 20h423.557l-70.162 69.824c-7.829 7.792-7.859 20.455-.067 28.284 7.793 7.831 20.457 7.858 28.285.068l104.504-104 .018-.019c7.833-7.818 7.808-20.522-.001-28.314z"-->
<!--      />-->
<!--    </svg>-->
    <svg width="40" height="40" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">

      <path d="m28.9584 19.2791-.3867.4419c-.2563.2928-.2563.7677 0 1.0607L39.2627 33l-10.691 12.2184c-.2563.2929-.2563.7678 0 1.0607l.3867.4419c.2563.2929.6718.2929.928 0l11.5419-13.1907c.2562-.2928.2562-.7677 0-1.0606L29.8864 19.2791c-.2562-.2929-.6717-.2929-.928 0Z" fill="#fff" stroke="#fff" stroke-width="3px"></path> <defs><filter id="a" x="0" y="0" width="68" height="68" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood> <feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"></feColorMatrix> <feOffset dy="1"></feOffset> <feGaussianBlur stdDeviation="2"></feGaussianBlur> <feComposite in2="hardAlpha" operator="out"></feComposite> <feColorMatrix values="0 0 0 0 0.392157 0 0 0 0 0.254902 0 0 0 0 0.647059 0 0 0 0.38 0"></feColorMatrix> <feBlend in2="BackgroundImageFix" result="effect1_dropShadow_416_47"></feBlend> <feBlend in="SourceGraphic" in2="effect1_dropShadow_416_47" result="shape"></feBlend></filter></defs></svg>
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
