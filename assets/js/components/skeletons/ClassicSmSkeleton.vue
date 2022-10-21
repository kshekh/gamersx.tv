<template>
  <div>
    <!-- ClassicSmSkeleton -->
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
          Classic Sm Skeleton
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
          pl-0
        "
      >
        <div
          v-for="i in 10"
          :key="i"
          class="
            mr-1.5
            xl:mr-3
            flex-shrink-0
            w-36
            md:w-28
            xl:w-48
            h-20
            md:h-18
            xl:h-32
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
  name: "ClassicSmSkeleton",
};
</script>
