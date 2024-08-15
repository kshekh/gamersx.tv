<script setup>
import { defineEmits, defineProps, onMounted, onUnmounted, ref } from "vue";
import { useCurrentElement } from "@vueuse/core/index.cjs";
import { useContainerStore } from "../../stores/containerStore";
import { useVideoStore } from "../../stores/VideoStore";
import CommonContainerIcon from "./CommonContainerIcon/CommonContainerIcon.vue";

const commonContainerRef = ref(null);
const isPinActive = ref(false);
const isMoveActive = ref(false);
const parentEl = ref(null);

const containerStore = useContainerStore();
const emit = defineEmits(["close-container"]);

const props = defineProps({
  customStyles: Array,
  innerWrapperClassNames: Array,
});

function disableContextMenu(event) {
  event.preventDefault();
}

function handleCloseEvent() {
  containerStore.$reset();
  emit("close-container");
}

function handleMoveEvent() {
  containerStore.$reset();
}

function handlePinEvent() {
  const container = parentEl.value;
  const { top, left } = container.getBoundingClientRect();

  // Unpin the container
  if (containerStore.isPinned) {
    isPinActive.value = false;

    container.style.transition = "none";
    container.style.position = "absolute";
    container.style.top = top + window.scrollY + "px";
    container.style.left = left + "px";
    containerStore.isPinned = false;
    containerStore.isPinBtnActive = false;
    containerStore.isPinnedContainer = false;

    return;
  }

  // Pin the container
  container.style.transition = "none";
  container.style.transform = "none";
  container.style.position = "fixed";
  container.style.top = top + "px";
  container.style.left = left + "px";

  isPinActive.value = true;

  containerStore.isPinned = true;
  containerStore.isPinBtnActive = true;
}

function setParentPosition() {
  const el = useCurrentElement(commonContainerRef);
  parentEl.value = el.value.parentElement;

  useVideoStore().setPosition(parentEl.value);
}

onMounted(() => {
  window.addEventListener("contextmenu", disableContextMenu);
  setParentPosition();
});

onUnmounted(() => {
  window.removeEventListener("contextmenu", disableContextMenu);
});
</script>

<template>
  <div
    :style="{ zIndex: '1000', ...customStyles }"
    class="w-[500px] h-[350px] common-container"
    ref="commonContainerRef"
  >
    <div
      oncontextmenu="false"
      class="actions--wrapper border-4 border-b-0 shadow-2xl shadow-purple-600 border-purple overflow-hidden common-container__actions"
    >
      <div
        @click="handlePinEvent"
        :class="['actions--btn', { 'actions--btn-active': isPinActive }]"
      >
        <CommonContainerIcon :icon-type="'pin'" />
      </div>
      <div
        @mousedown="handleMoveEvent"
        @dragstart="() => false"
        :class="['actions--btn', { 'actions--btn-move-disabled': isPinActive }]"
      >
        <CommonContainerIcon :icon-type="'move'" />
      </div>
      <div @click="handleCloseEvent" class="actions--btn">
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

<style lang="css" scoped>
@import "./CommonContainer.css";

.common-container__body {
  background: none;
  outline: none;
  transition: 1s background ease-in;
}

.common-container__actions {
  height: 30px;
  width: 180px;
  display: flex;
  position: relative;
  top: 0;
  right: 0;
  left: calc(100% - 177px);
  opacity: 0;
  transition: 1s opacity;
  /*border-bottom: 1px solid black;*/
  z-index: 30;
  background-color: #fff;
  border: 3px solid #7a4ecc;
  border-bottom: none;
  border-radius: 10px 10px 0 0;
}

.common-container__actions .actions--btn {
  border-top: 3px solid #000;
  overflow: hidden;
}

.common-container__actions .actions--btn:nth-of-type(1) {
  border-left: 3px solid #000;
  border-radius: 5px 0 0 0;
}

.common-container__actions .actions--btn:nth-of-type(2) {
  border-left: 1px solid #000;
  border-right: 1px solid #000;
}

.common-container__actions .actions--btn:nth-last-of-type(1) {
  border-right: 3px solid #000;
  border-radius: 0 5px 0 0;
}
</style>
