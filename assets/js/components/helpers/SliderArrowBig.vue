<template>
  <button
    @click="handleClick()"
    class="
            ml-2
            md:ml-5
            xl:ml-8
            flex-shrink-0
            relative
            z-10
            transition-all
            duration-300
            transform

          "
    :class="[
      videoTypeClass,
      {
        'transform rotate-180': !isNext
      }
    ]"
  >
    <svg
      width="68"
      height="68"
      viewBox="0 0 68 68"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <g filter="url(#a)">
        <circle
          cx="34"
          cy="33"
          r="29"
          stroke="currentColor"
          stroke-width="2"
          shape-rendering="geometricPrecision"
        />
      </g>
      <path
        d="m28.9584 19.2791-.3867.4419c-.2563.2928-.2563.7677 0 1.0607L39.2627 33l-10.691 12.2184c-.2563.2929-.2563.7678 0 1.0607l.3867.4419c.2563.2929.6718.2929.928 0l11.5419-13.1907c.2562-.2928.2562-.7677 0-1.0606L29.8864 19.2791c-.2562-.2929-.6717-.2929-.928 0Z"
        fill="#fff"
      />
      <defs>
        <filter
          id="a"
          x="0"
          y="0"
          width="68"
          height="68"
          filterUnits="userSpaceOnUse"
          color-interpolation-filters="sRGB"
        >
          <feFlood flood-opacity="0" result="BackgroundImageFix" />
          <feColorMatrix
            in="SourceAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
            result="hardAlpha"
          />
          <feOffset dy="1" />
          <feGaussianBlur stdDeviation="2" />
          <feComposite in2="hardAlpha" operator="out" />
          <feColorMatrix
            values="0 0 0 0 0.392157 0 0 0 0 0.254902 0 0 0 0 0.647059 0 0 0 0.38 0"
          />
          <feBlend
            in2="BackgroundImageFix"
            result="effect1_dropShadow_416_47"
          />
          <feBlend
            in="SourceGraphic"
            in2="effect1_dropShadow_416_47"
            result="shape"
          />
        </filter>
      </defs>
    </svg>
  </button>
</template>

<script>
export default {
  name: "SliderArrowBig",
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
        case "YouTubeEmbed":
          className = "text-red";
          break;
        case "TwitchEmbed":
          className = "text-purple";
          break;
      }

      return className;
    }
  },
  methods: {
    handleClick() {
      this.$emit("arrow-big-clicked");
    }
  }
};
</script>
