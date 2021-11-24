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
      };
    }
  }
};