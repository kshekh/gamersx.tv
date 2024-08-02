<template>
  <div
    :class="[...classNames]"
    :style="{ zIndex: '1000', ...customStyles }"
    class="w-[500px] h-[350px] common-container"
  >
    <div oncontextmenu="false" class="actions--wrapper border-4 border-b-0 shadow-2xl shadow-purple-600 border-purple overflow-hidden common-container__actions">
      <div
        @click="(event) => $emit('on-pin', event)"
        :class="['actions--btn', { 'actions--btn-active': isPinActive }]"
      >
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
<!--    bg-black border-3 outline outline-[3px] outline-[#7A4ECC] !shadow-2xl !shadow-purple-600-->
    <div
      class="w-full h-full p-14 flex flex-col relative rounded-[10px] rounded-tr-none common-container__body"
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
