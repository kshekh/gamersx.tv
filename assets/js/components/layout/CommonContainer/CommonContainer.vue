<template>
  <div
    :class="[...classNames]"
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
    CommonContainerIcon: CommonContainerIcon,
  },
  emits: ["close-container", "on-pin", "on-mouse-down"],
  props: [
    "classNames",
    "innerWrapperClassNames",
    "customStyles",
    "isPinActive",
    "isMoveActive",
  ],
  methods: {
    disableContextMenu(event) {
      event.preventDefault();
    }
  },
  mounted() {
    document.addEventListener("contextmenu", this.disableContextMenu);
  },
  beforeDestroy() {
    document.removeEventListener("contextmenu", this.disableContextMenu);
  }
};
</script>

<style lang="css" scoped>
@import "./CommonContainer.css";
</style>
