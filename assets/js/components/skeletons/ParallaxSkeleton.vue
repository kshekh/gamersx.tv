<template>
  <div>
    <!-- ParallaxSkeleton -->
    <div>
      <div class="pl-8 md:pl-10 xl:pl-24">
        <h2
          class="
            inline-block
            text-transparent
            font-calibri font-bold
            text-sm
            md:text-2xl
            xl:text-4xl
            bg-grey-600
            rounded-md
            animate-pulse
          "
        >
          Parallax Skeleton
        </h2>
      </div>
      <div
        class="
          flex
          overflow-hidden
          pt-5
          xl:pt-9
          pb-7
          md:pb-6
          xl:pb-12
          pl-4
          xl:pl-20
        "
      >
        <div
          v-for="i in 10"
          :key="i"
          class="
            xl:max-w-485
            w-full
            md:w-1/3
            xl:w-full
            h-41
            md:h-22
            xl:h-41
            flex-shrink-0
            bg-grey-600
            animate-pulse
          "
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ParallaxSkeleton"
};
</script>
