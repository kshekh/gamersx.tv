<template>
  <div v-show="show" :class="{ 'opacity-0': !value, 'opacity-100': value }"
    class="transition-opacity duration-500 fixed inset-0 flex items-end justify-center bg-black/[.35] z-50 backdrop-filter backdrop-blur"
    @click="handleClickModal">
    <div class="absolute p-5 w-full bg-black border-2 border-purple/[.5] border-b-0 border-l-0 border-r-0 space-y-3"
      :style="{ maxWidth: '100%' }">
      <div class="gm-modal-content flex flex-col h-full overflow-hidden" :style="{ paddingBottom: '0' }">
        <div class="gm-modal-body flex flex-col md:flex-row space-y-4 md:space-y-0 md:items-start md:justify-start">
          <slot></slot>
          <slot name="buttonSlot"></slot> <!-- adding an additional slot -->
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      required: true
    },
    width: {
      type: String,
      default: "700px"
    },
    noCloseOnBackdrop: {
      type: Boolean,
      default: false
    },
    title: {
      type: String
    }
  },
  methods: {
    handleClickModal(e) {
      if (!this.noCloseOnBackdrop && !e.target.closest(".gm-modal-content")) {
        this.$emit("input", false);
      }
    }
  },
  data: function () {
    return {
      show: false
    };
  },
  watch: {
    value: {
      handler(isVisible) {
        if (isVisible) {
          this.show = true;
        } else {
          setTimeout(() => {
            this.show = false;
          }, 500); // this setTimeout will sync with transition-duration value in the modal wrapper class. They should be the same.
        }
      },
      immediate: true
    }
  }
};
</script>