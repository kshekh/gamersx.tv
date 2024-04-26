<template>
  <div class="flex flex-row flex-grow flex-wrap justify-center p-8">
    <div v-for="stream in streams" class="rounded-md m-8 p-4">
      <component
        :is="stream.componentName"
        v-bind="stream"
      ></component>
    </div>
  </div>
</template>
<script>
import EmbedContainer from '../layout/EmbedContainer/EmbedContainer.vue'

export default {
  name: 'EmbedTab',
  components: {
    'EmbedContainer': EmbedContainer,
  },
  props: ['streams'],
}
</script>
