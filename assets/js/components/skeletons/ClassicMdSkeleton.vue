<template>
  <div>
    <!-- ClassicMdSkeleton -->
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
          Classic Md Skeleton
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
            flex-shrink-0
            bg-grey-600
            rounded-md
            animate-pulse
            mr-3
            md:mr-2
            xl:mr-4
            w-64
            md:w-36
            xl:w-64
            h-48
            md:h-28
            xl:h-48
          "
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ClassicMdSkeleton"
};
</script>
