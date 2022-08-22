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
    <!-- FullWidthDescriptiveSkeleton -->
    <div class="container mx-auto">
      <div class="pb-50p"></div>
      <div
        class="
          px-4
          md:px-12
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
