<template>
  <div
    class="home-row
      mb-7
      md:mb-9
      xl:mb-14
      overflow-hidden
      min-h-mobile
      relative"
  >
    <!-- FullWidthImagerySkeleton -->
    <div class="container mx-auto">
      <div class="pb-50p"></div>
      <div
        class="
          pr-4
          md:pr-12
          py-2.5
          md:py-6
          xl:py-11
          flex
          items-center
          justify-center
          absolute
          inset-0
          z-10
        bg-grey-600
          animate-pulse
        "
      ></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FullWidthDescriptiveSkeleton"
};
</script>
