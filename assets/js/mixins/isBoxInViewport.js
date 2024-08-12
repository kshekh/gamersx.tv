export default {
  methods: {
    checkIfBoxInViewPort() {
      const docViewTop = window.scrollY;
      const docViewBottom = docViewTop + window.innerHeight;

      const elemCoordinates = this.$refs.embedWrapper.getBoundingClientRect();
      const elemTop = elemCoordinates.top + window.scrollY;
      const elemBottom = elemCoordinates.bottom + window.scrollY;

      if (((elemBottom <= docViewTop) || (elemTop >= docViewBottom))) {
        this.scrollOut();
      }
    },
    checkScrollPosition() {
      const docViewTop = window.scrollY;
      const docViewBottom = docViewTop + window.innerHeight;

      const elemCoordinates = this.$refs.embedWrapper.getBoundingClientRect();
      const elemTop = elemCoordinates.top + window.scrollY;
      const elemBottom = elemCoordinates.bottom + window.scrollY;
      console.log(docViewTop, 'docViewTop')
      console.log(docViewBottom, 'docViewBottom')
      console.log(elemTop, 'elemTop')
      console.log(elemBottom, 'elemBottom')
      if (((elemBottom <= docViewTop) || (elemTop >= docViewBottom))) {
        console.log('I have scrolled out');
        this.scrollOut();
      } else {
        console.log('I am scrolled in');
        this.scrollIn();
      }
    },
    checkIfInOriginalViewport() {
      const docViewTop = window.scrollY;
      const docViewBottom = docViewTop + window.innerHeight;

      const originalCoordinates = this.baseCoordinates;
      if (!originalCoordinates) {
        return;
      }

      const elemTop = originalCoordinates.top;
      const elemBottom = originalCoordinates.bottom;

      // Calculate 10% thresholds (instead of 90%)
      const elemTop10Percent = elemTop + 0.1 * originalCoordinates.height;
      const elemBottom10Percent = elemBottom - 0.1 * originalCoordinates.height;

      // Check if more than 10% of the element is within the viewport
      if (elemBottom10Percent >= docViewTop && elemTop10Percent <= docViewBottom) {
        if (!this.isScrolledIn) {
          console.log('More than 10% visible - scrollIn');
          this.scrollIn();
        }
      } else {
        if (this.isScrolledIn) {
          console.log('10% or less visible - scrollOut');
          this.scrollOut();
        }
      }
    },
  }
}
