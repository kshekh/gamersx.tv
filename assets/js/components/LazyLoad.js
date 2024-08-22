import { h, defineAsyncComponent } from "vue";

export default function lazyLoadComponent({
  componentFactory,
  loading,
  loadingData,
}) {
  let resolveComponent;

  return defineAsyncComponent({
    loader: () =>
      new Promise((resolve) => {
        resolveComponent = resolve;
      }),
    loadingComponent: {
      mounted() {
        if (!("IntersectionObserver" in window)) {
          componentFactory().then(resolveComponent);
          return;
        }

        const observer = new IntersectionObserver((entries) => {
          if (entries[0].intersectionRatio <= 0) return;

          observer.unobserve(this.$el);
          componentFactory().then(resolveComponent);
        });
        observer.observe(this.$el);
      },
      render() {
        return h(loading, loadingData);
      },
    },
  });
}
