import { ref, onMounted, onBeforeUnmount } from "vue";

export function useViewportChecker(refElement) {
  const isScrolledIn = ref(true); // Assume initially in viewport
  const baseCoordinates = ref(null);

  function getElementCoordinates() {
    if (!refElement.value) return null;
    const rect = refElement.value.getBoundingClientRect();
    return {
      top: rect.top + window.scrollY,
      bottom: rect.bottom + window.scrollY,
      height: rect.height,
    };
  }

  function checkIfInOriginalViewport() {
    const docViewTop = window.scrollY;
    const docViewBottom = docViewTop + window.innerHeight;

    if (!baseCoordinates.value) return;

    const elemTop = baseCoordinates.value.top;
    const elemBottom = baseCoordinates.value.bottom;

    const elemTop10Percent = elemTop + 0.1 * baseCoordinates.value.height;
    const elemBottom10Percent = elemBottom - 0.1 * baseCoordinates.value.height;

    // Check if 10% or less of the element is within the viewport
    const isTenPercentOrLessVisible = !(
      elemBottom10Percent >= docViewTop && elemTop10Percent <= docViewBottom
    );

    isScrolledIn.value = !isTenPercentOrLessVisible;

    if (isTenPercentOrLessVisible) {
      console.log("10% or less visible");
    } else {
      console.log("More than 10% visible");
    }
  }

  function setBaseCoordinates() {
    baseCoordinates.value = getElementCoordinates();
  }

  onMounted(() => {
    setBaseCoordinates();
    window.addEventListener("scroll", checkIfInOriginalViewport);
  });

  onBeforeUnmount(() => {
    window.removeEventListener("scroll", checkIfInOriginalViewport);
  });

  return { isScrolledIn };
}
