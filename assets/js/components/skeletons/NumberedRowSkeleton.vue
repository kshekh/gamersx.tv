<template>
  <div>
    <!-- NumberedRowSkeleton -->
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
          Numbered Row Skeleton
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
            flex items-end
          "
        >
          <span
            :data-number="i"
            class="transform translate-x-3 leading-extra-tight md:translate-x-4 xl:translate-x-5 shrink-0 font-bahnschrift font-semibold text-8xl md:text-xxl xl:text-3xxl text-stroke animate-pulse"
            :class="{
              'translate-x-8 md:translate-x-10 xl:translate-x-12': i >= 10
            }"
          >
            {{ i }}
          </span>
          <div
            class="
              flex
              items-center
              w-18
              h-24
              md:w-24
              md:h-32
              xl:w-28
              xl:h-40
              shrink-0
              bg-grey-600
              rounded-md
              animate-pulse
            "
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NumberedRowSkeleton"
};
</script>
