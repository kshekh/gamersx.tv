<template>
  <svg
    class="container-icon"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      v-if="iconType === 'close'"
      d="M18 6L6 18"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'close'"
      d="M6 6L18 18"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'pin'"
      d="M10 12.89898V17.999C10 18.2642 10.1054 18.5186 10.2929 18.7061C10.4804 18.8936 10.7348 18.999 11 18.999C11.2652 18.999 11.5196 18.8936 11.7071 18.7061C11.8946 18.5186 12 18.2642 12 17.999V12.89898C13.214 12.65116 14.2928 11.96139 15.0272 10.9634C15.7616 9.96539 16.0992 8.73026 15.9747 7.49746C15.8502 6.26465 15.2723 5.12201 14.3532 4.29104C13.434 3.46008 12.2391 3 11 3C9.76093 3 8.56599 3.46008 7.64686 4.29104C6.72772 5.12201 6.14987 6.26465 6.02533 7.49746C5.90079 8.73026 6.23844 9.96539 6.97281 10.9634C7.70719 11.9614 8.78597 12.6512 10 12.89898ZM11 4.99898C11.5934 4.99898 12.1734 5.17493 12.6667 5.50457C13.1601 5.83422 13.5446 6.30275 13.7716 6.85093C13.9987 7.39911 14.0581 8.00231 13.9424 8.58425C13.8266 9.16619 13.5409 9.70074 13.1213 10.1203C12.7018 10.5399 12.1672 10.8256 11.5853 10.9413C11.0033 11.0571 10.4001 10.9977 9.85196 10.7706C9.30378 10.5436 8.83525 10.159 8.5056 9.66569C8.17596 9.17234 8 8.59232 8 7.99898C8 7.20333 8.31608 6.44027 8.87869 5.87766C9.4413 5.31505 10.2044 4.99898 11 4.99898Z"
      fill="black"
    />
    <path
      v-if="iconType === 'move'"
      d="M6 10L4 12L6 14"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'move'"
      d="M10 6L12 4L14 6"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'move'"
      d="M14 18L12 20L10 18"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'move'"
      d="M18 10L20 12L18 14"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'move'"
      d="M4 12H20"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      v-if="iconType === 'move'"
      d="M12 4V20"
      stroke="black"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>

<script>
export default {
  name: "CommonContainerIcon",
  props: ["iconType"],
};
</script>

<style lang="css" scoped>
@import "./CommonContainerIcon.css";
</style>
