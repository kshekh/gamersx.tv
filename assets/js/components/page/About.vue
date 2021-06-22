<template>
  <div class="pt-6 font-bold text-xl">
    <p class="rounded-md text-left text-xl">
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
