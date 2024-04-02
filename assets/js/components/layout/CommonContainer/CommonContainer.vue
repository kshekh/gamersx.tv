<template>
  <div
    :class="[...classNames]"
    :style="{ zIndex: '1000', ...customStyles }"
    class="w-full h-full"
  >
    <div class="actions--wrapper">
      <div
        @click="(event) => $emit('on-pin', event)"
        :class="['actions--btn', { 'actions--btn-active': isPinActive }]"
      >
        <div class="left--border"></div>
        <img
          :src="getButtonIcon('pin')"
          :class="`actions--btn-icon`"
          alt="action button pin"
        />
      </div>
      <div
        @mousedown="(event) => $emit('on-mouse-down', event)"
        @dragstart="() => false"
        :class="['actions--btn', { 'actions--btn-move-disabled': isPinActive }]"
      >
        <img
          :src="getButtonIcon('move')"
          class="actions--btn-icon"
          alt="action button move"
        />
      </div>
      <div @click="$emit('close-container')" class="actions--btn">
        <img
          :src="getButtonIcon('close')"
          class="actions--btn-icon"
          alt="action button close"
        />
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
export default {
  name: "CommonContainer",
  emits: ["close-container", "on-pin", "on-mouse-down"],
  props: [
    "classNames",
    "innerWrapperClassNames",
    "customStyles",
    "isPinActive",
    "isMoveActive",
  ],

  methods: {
    getButtonIcon(iconName) {
      return require(`../../assets/icons/${iconName}.svg`);
    },
  },
};
</script>

<style lang="css" scoped>
@import "./CommonContainer.css";
</style>
