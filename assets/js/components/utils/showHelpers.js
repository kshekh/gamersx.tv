import { computed } from "vue";

export function useShowHelpers(props) {
  console.log("show", props);
  const showArt = computed(
    () =>
      (props.showOnline && props.onlineDisplay.showArt) ||
      (!props.showOnline && props.offlineDisplay.showArt),
  );

  function showChannel(channel) {
    return (
      (channel.showOnline &&
        (channel.onlineDisplay.showArt ||
          channel.onlineDisplay.showEmbed ||
          channel.onlineDisplay.showOverlay)) ||
      (!channel.showOnline &&
        (channel.offlineDisplay.showArt ||
          channel.offlineDisplay.showEmbed ||
          channel.offlineDisplay.showOverlay))
    );
  }

  const showEmbed = computed(
    () =>
      (props.showOnline && props.onlineDisplay.showEmbed) ||
      (!props.showOnline && props.offlineDisplay.showEmbed),
  );

  const showOverlay = computed(
    () =>
      props.overlay &&
      ((props.showOnline && props.onlineDisplay.showOverlay) ||
        (!props.showOnline && props.offlineDisplay.showOverlay)),
  );

  return { showArt, showChannel, showEmbed, showOverlay };
}
