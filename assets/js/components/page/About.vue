<template>
  <div class="p-6 font-bold text-xl">
    <p class="rounded-md text-center text-xl">
      {{ description }}
    </p>
  </div>
</template>
<script>
export default {
  name: 'About',
  props: ['description'],
}
</script>
