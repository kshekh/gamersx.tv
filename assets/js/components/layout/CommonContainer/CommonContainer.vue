<template>
  <div
    :class="computedClassNames"
    :style="{ zIndex: '1000', ...customStyles }"
    class="w-full h-full"
  >
    <div oncontextmenu="false" class="actions--wrapper">
      <div
        @click="(event) => $emit('on-pin', event)"
        :class="['actions--btn', { 'actions--btn-active': isPinActive }]"
      >
        <div class="left--border"></div>
        <CommonContainerIcon :icon-type="'pin'" />
      </div>
      <div
        @mousedown="(event) => $emit('on-mouse-down', event)"
        @dragstart="() => false"
        :class="['actions--btn', { 'actions--btn-move-disabled': isPinActive }]"
      >
        <CommonContainerIcon :icon-type="'move'" />
      </div>
      <div @click="$emit('close-container')" class="actions--btn">
        <CommonContainerIcon :icon-type="'close'" />
      </div>
    </div>
    <div
      class="w-full h-full flex flex-col relative cut-edge__clipped cut-edge__clipped--sm-border cut-edge__clipped-top-left-sm bg-black"
      :class="innerWrapperClassNames"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script>
import CommonContainerIcon from "./CommonContainerIcon/CommonContainerIcon.vue";

export default {
  name: "CommonContainer",
  components: {
    CommonContainerIcon,
  },
  emits: ["close-container", "on-pin", "on-mouse-down"],
  props: {
    classNames: {
      type: [Array, String],
      default: () => [],
    },
    innerWrapperClassNames: {
      type: [Array, String],
      default: () => [],
    },
    customStyles: {
      type: Object,
      default: () => ({}),
    },
    isPinActive: {
      type: Boolean,
      default: false,
    },
    isMoveActive: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    computedClassNames() {
      return Array.isArray(this.classNames) ? this.classNames : [this.classNames];
    },
  },
  methods: {
    disableContextMenu(event) {
      event.preventDefault();
    },
  },
  mounted() {
    document.addEventListener("contextmenu", this.disableContextMenu);
  },
  beforeDestroy() {
    document.removeEventListener("contextmenu", this.disableContextMenu);
  },
};
</script>

<style lang="css">
@import "./CommonContainer.css";
</style>