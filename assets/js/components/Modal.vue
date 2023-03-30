<template>
  <div
    v-if="value"
    class="fixed min-w-full min-h-full top-0 left-0 bg-black/[.84] z-50"
    @click="handleClickModal"
  >
    <div class="relative w-full h-screen top-0 left-0">
      <div
        class="absolute px-5 left-1/2 -translate-x-1/2 wide:top-0 top-10 sm:top-32 w-full max-h-screen overflow-y-auto"
        :style="{ maxWidth: width }"
      >
        <div class="gm-modal-content bg-black border-2 border-purple p-5">
          <div class="gm-modal-header" v-if="title">{{ title }}</div>
          <div class="gm-modal-body">
            <slot></slot>
          </div>
          <div class="gm-modal-footer">
            <slot name="footer">
              <div class="flex justify-center items-center pt-5">
                <button
                  class="border-2 border-purple px-4 py-3 text-purple hover:bg-purple hover:text-white leading-none transition-all"
                  @click="$emit('ok')"
                >
                  {{ okTitle }}
                </button>
              </div>
            </slot>
          </div>
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
    },
    okTitle: {
      type: String,
      default: "OK"
    }
  },
  methods: {
    handleClickModal(e) {
      if (!this.noCloseOnBackdrop && !e.target.closest(".gm-modal-content")) {
        this.$emit("input", false);
      }
    }
  }
};
</script>
