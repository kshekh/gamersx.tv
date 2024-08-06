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
      const docViewTop = window.scrollY;
      const docViewBottom = docViewTop + window.innerHeight;

      const elemCoordinates = this.$refs.embedWrapper.getBoundingClientRect();
      const elemTop = elemCoordinates.top + window.scrollY;
      const elemBottom = elemCoordinates.bottom + window.scrollY;
      console.log(docViewTop, 'docViewTop')
      console.log(docViewBottom, 'docViewBottom')
      console.log(elemTop, 'elemTop')
      console.log(elemBottom, 'elemBottom')
      if ( ((elemBottom <= docViewTop) || (elemTop >= docViewBottom)) ) {
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
      // console.log('current coordinates (top)', docViewTop);
      // console.log('current coordinates (bottom)', docViewBottom);

      const originalCoordinates = this.baseCoordinates;
      if (!originalCoordinates) {
        return;
      }

      const elemTop = originalCoordinates.top;
      const elemBottom = originalCoordinates.bottom;
      // console.log('original coordinates (top)', elemTop);
      // console.log('original coordinates (bottom)', elemBottom);

      if (elemBottom <= docViewTop || elemTop >= docViewBottom) {
        console.log('I am out');
        this.scrollOut();
      } else {
        console.log('I am in');
        this.scrollIn();
      }
    }
  }
};