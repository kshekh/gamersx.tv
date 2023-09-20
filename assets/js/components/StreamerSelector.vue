<template>
  <div v-if="rows.length">
    <h3>Select Streamer of {{ gameName }}</h3>
    <table id="streamer-table" class="table table-bordered table-striped table-hover sonata-ba-list">
      <thead>
        <tr class="sonata-ba-list-field-header">
          <th v-for="header in headers" class="sonata-ba-list-field-header">{{ header }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in rows" @click="selectRow(row)">
          <template v-for="field in row.fields">
            <td class="sonata-ba-list-field handle"><i class="fa fa-arrows"></i> {{ field.streamer }}</td>
            <td class="sonata-ba-list-field">{{ field.viewer_count }}</td>
            <td class="sonata-ba-list-field">
              <input type="hidden" name="streamer_index[]" :value="row.id" >
              <input type="checkbox" :name="'is_blacklisted_'+row.id" :checked="field.is_blacklisted" >
            </td>
          </template>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import axios from 'axios';
import tableDragger from 'table-dragger';

export default {
  components: {
  },
  data: function()  {
    return {
      itemType: '',
      headers: ['Streamer', 'Viewers', 'Blacklist'],
      message: null,
      searchValue: null,
      isSearching: false,
      rows: [],
      page: 15,
      cursor: '',
      gameName:'',
      gameId:'',
      howRowItemId:'',
    }
  },
  methods: {
    /* Clicking on a row writes to the Admin form */
    selectRow: function(row) {
      console.log('selected row:',row);
    },
    /** == Call the API to get various kinds of results, then process them == **/
    getGamerStreamerResults: function(params) {
      var game_id = document.querySelector('input.topic-id').value;
      var game_name = document.querySelector('input.topic-label').value;
      this.gameId = game_id;
      this.gameName = game_name;
      if(this.gameId != '') {
        this.callApi('/api/streams/'+this.gameId, params, this.processGameStreamerResults);
      }
    },
    getPagerParams: function(direction) {
      let params = {
        'first': this.page,
        'how_row_item_id': $("#how_row_item_id").val()
      }
      if (this.cursor && direction === 'before') {
        params['before'] = this.cursor;
      } else if (this.cursor && direction === 'after') {
        params['after'] = this.cursor;
      }
      return params;
    },
    moreResults(direction) {
      this.message = null;
      let params = this.getPagerParams(direction);
      this.getGamerStreamerResults(params);
    },
    callApi(url, params, callback) {
      axios
        .get(url, { params: params })
        .then(response => callback(response))
    },
    processGameStreamerResults(response) {
      this.cursor = response.data.pagination.cursor;
      if (response.data.data === null) {
        this.message = 'Sorry, no results for that query';
        return;
      }
      this.headers = ['Streamer', 'Viewers', 'Blacklist'];
      this.rows = response.data.data.map(function(stream) {
        let row = {
          fields: [{
              'streamer':stream.user_name,
              'viewer_count': stream.viewer_count,
              'is_blacklisted': stream.is_blacklisted,
            }
          ]
        };
        row.id = stream.id;
        row.game_id = stream.game_id;

        return row;
      });

    },

  },
  created() {
    // Listen for the event
    EventBus.$on('selected-game', eventData => {
      console.log('selected-game',eventData);
      this.gameId = eventData.game_id;
      this.gameName = eventData.game_name
      this.moreResults();
    });

    EventBus.$on('clear-game-streamers', eventData => {
      console.log('clear-game-streamers');
      this.rows = [];
    });

  },
  mounted: function() {
    this.moreResults();
  },
  updated: function () {
    var el = document.getElementById('streamer-table');
    if($('#streamer-table td').hasClass('handle')) {
      var dragger = tableDragger(el, {
        mode: 'row',
        dragHandler: '.handle',
        onlyBody: true,
        animation: 300
      });
      dragger.on('drop',function(from, to){
        console.log(from);
        console.log(to);
      });
    }
  },
}
</script>
<style scoped>
tbody tr {
  cursor: pointer;
}
</style>
