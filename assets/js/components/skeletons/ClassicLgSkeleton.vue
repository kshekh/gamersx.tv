<template>
  <div>
    <!-- ClassicLgSkeleton -->
    <div>
      <div class="px-4">
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
          Classic Lg Skeleton
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
        "
      >
        <div
          v-for="i in 10"
          :key="i"
          class="
            shrink-0
            mr-3
            md:mr-2
            xl:mr-4
            w-60
            md:w-44
            xl:w-72
            h-40
            md:h-28
            xl:h-48
            bg-grey-600
            rounded-md
            animate-pulse
          "
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ClassicLgSkeleton",
};
</script>
