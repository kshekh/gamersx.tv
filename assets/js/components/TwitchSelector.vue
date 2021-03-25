<template>
  <div>
    <table class="table table-bordered table-striped table-hover sonata-ba-list">
      <tbody>
        <tr v-for="stream in streams">
          <td class="sonata-ba-list-field">
            {{ stream.user_name }} playing {{ stream.game_name }} for {{ stream.viewer_count }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</template>
<script>
import axios from 'axios';

export default {
  components: {
  },
  data: function()  {
    return {
      streams: []
    }
  },
  methods: {
  },
  mounted: function() {
    axios
      .get('/api/stream/popular')
      .then(response => {
        this.streams = response.data.data;
      });
  }
}
</script>
<style scoped>
</style>
