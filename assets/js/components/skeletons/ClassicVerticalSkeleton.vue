<template>
  <div>
    <!-- ClassicVerticalSkeleton -->
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
          Classic Vertical Skeleton
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
            w-26
            md:w-36
            xl:w-60
            h-34
            md:h-46
            xl:h-80
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
  name: "ClassicVerticalSkeleton"
};
</script>
