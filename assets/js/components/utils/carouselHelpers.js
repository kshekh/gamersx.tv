export function useCarouselHelpers(props) {
  function first() {
    props.rowIndex.value = 0;
    reorder();
  }

  function backward() {
    props.rowIndex.value = (props.rowIndex.value - 1).mod(
      props.displayChannels.value.length,
    );
    reorder();
  }

  function forward() {
    props.rowIndex.value = (props.rowIndex.value + 1).mod(
      props.displayChannels.value.length,
    );
    reorder();
  }

  function reorder() {
    checkMouseActive();
    for (let i = 0; i < props.channelDivs.value.length; i++) {
      let j = (i - props.rowIndex.value).mod(props.channelDivs.value.length);
      props.channelDivs.value[i].style.order = j + 1;
    }
  }

  return { first, backward, forward, reorder };
}
