export default {
  methods: {
    checkIfBoxInViewPort() {
      const docViewTop = window.scrollY;
      const docViewBottom = docViewTop + window.innerHeight;

      const elemCoordinates = this.$refs.embedWrapper.getBoundingClientRect();
      const elemTop = elemCoordinates.top + window.scrollY;
      const elemBottom = elemCoordinates.bottom + window.scrollY;

      if ( ((elemBottom <= docViewTop) || (elemTop >= docViewBottom)) ) {
        this.scrollOut();
      }
    },
    checkScrollPosition() {
      const embedWrapper = this.$refs.embedWrapper;
      if (!embedWrapper) {
        console.log('embedWrapper not found');
        return;
      }

      const scrollPosition = window.scrollY + window.innerHeight;
      const embedPosition = embedWrapper.offsetTop + (embedWrapper.offsetHeight * 0.75);
      console.log('scrollPosition:', scrollPosition, 'embedPosition:', embedPosition);
      if (scrollPosition > embedPosition && !this.hasScrolledPast75) {
        this.hasScrolledPast75 = true;
        this.clickContainer(this.currentChannel.embedData.elementId, true);
      }
    }
  }
};