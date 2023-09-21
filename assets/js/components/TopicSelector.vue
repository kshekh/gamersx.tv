<template>
  <div>
    <div class="form-group">
      <input name="topic-search" class="form-control" v-model="searchValue" type="search"
        placeholder="Enter a search term or page through the most popular streams"></input>
      <div class="well well-small">
        <button @click="search()" type="button" class="btn btn-small btn-primary">Search</button>
        <button @click="clear()" type="button" class="btn btn-small btn-primary">Clear Selected Item</button>
        <button @click="moreResults('before')" type="button" class="btn btn-small btn-primary">Previous Results</button>
        <button @click="moreResults('after')" type="button" class="btn btn-small btn-primary">Next Results</button>
        <button @click="clearSearch()" type="button" class="btn btn-small btn-primary">Reset Results</button>
      </div>
      <div v-if="message" class="well well-small">
        <span class="message">{{ message }}</span>
      </div>
    </div>
    <table class="table table-bordered table-striped table-hover sonata-ba-list">
      <thead>
        <tr class="sonata-ba-list-field-header">
          <th v-for="header in headers" class="sonata-ba-list-field-header">{{ header }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in rows" @click="selectRow(row)">
          <td v-for="field in row.fields" class="sonata-ba-list-field">{{ field }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import axios from 'axios';

export default {
  components: {
  },
  data: function()  {
    return {
      itemType: '',
      headers: ['Streamer', 'Game', 'Viewers'],
      message: null,
      searchValue: null,
      isSearching: false,
      rows: [],
      page: 15,
      cursor: '',
    }
  },
  methods: {
    /* Clicking on a row writes to the Admin form */
    selectRow: function(row) {
      var topic_id = '';
      var topic_label = '';
      if (this.itemType === 'youtube') {
        topic_id = this.searchValue;
        topic_label = 'YT: ' + this.searchValue;
      } else {
        topic_id = row.id;
        topic_label = row.label;
      }

      document.querySelector('input.topic-id').value = topic_id;
      document.querySelector('input.topic-label').value = topic_label;

      let params = {
        'item_type': this.itemType,
        'topic_id': topic_id,
        'how_row_item_id': $("#how_row_item_id").val()
      }

      this.callApi('/api/check_unique_container/', params, this.checkUniqueContainerResults);

      if (this.itemType === 'game') {
        let rowData = {
          'game_id': row.id,
          'game_name': row.label,
        };
        EventBus.$emit('selected-game', rowData);
      }
    },
    checkUniqueContainerResults(response) {
      if(response.data && response.data.is_unique_container) {
        toastr.error("Selected video/game already exists in another container. Please select different one or allow this one to repeat.");
        document.querySelector('input.topic-id').value = '';
        document.querySelector('input.topic-label').value = '';
      }
    },
    /* Clicking on the search button */
    search: function() {
      if (!this.isSearching) {
        this.cursor = '';
        this.isSearching = true;
      }
      this.moreResults();
    },
    /* Clearing search switches back to a popular search */
    clearSearch: function() {
      this.isSearching = false;
      this.searchValue = null;
      this.cursor = '';
      this.moreResults();
    },
    /* Remove the current selection */
    clear: function() {
      document.querySelector('input.topic-id').value = null;
      document.querySelector('input.topic-label').value = null;
      EventBus.$emit('clear-game-streamers');
    },
    /** == Call the API to get various kinds of results, then process them == **/
    getSearchResults: function(params) {
      if (this.itemType === 'game') {
        this.callApi('/api/query/game/' + this.searchValue, params, this.processGameResults);
      } else if (this.itemType === 'streamer') {
        this.callApi('/api/query/streamer/' + this.searchValue, params, this.processStreamerResults);
      } else if (this.itemType === 'channel') {
        this.callApi('/api/query/channel/' + this.searchValue, params, this.processChannelResults);
      } else if (this.itemType === 'youtube') {
        this.callApi('/api/query/youtube/' + this.searchValue, params, this.processYoutubeResults);
      }
    },
    getPopularResults: function(params) {
      this.callApi('/api/stream/popular', params, this.processPopularResults);
    },

    getPagerParams: function(direction) {
      let params = {
        'first': this.page
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
      if (this.isSearching) {
        this.getSearchResults(params);
      }  else {
        this.getPopularResults(params);
      }
    },

    callApi(url, params, callback) {
      axios
        .get(url, { params: params })
        .then(response => callback(response))
    },

    /** Callbacks used after getting data from the API **/
    processGameResults(response) {
      this.cursor = response.data.pagination.cursor;
      this.headers = ['Id', 'Name'];
      if (response.data.data === null) {
        this.message = 'Sorry, no results for that query';
        return;
      }
      this.rows = response.data.data.map(function(game) {
        return {
          fields: [
            game.id,
            game.name,
          ],
          id: game.id,
          label: game.name,
        };
      });
    },
    processStreamerResults(response) {
      this.cursor = response.data.pagination.cursor;
      if (response.data.data === null) {
        this.message = 'Sorry, no results for that query';
        return;
      }
      this.headers = ['Id', 'Name'];
      this.rows = response.data.data.map(function(channel) {
        return {
          fields: [
            channel.id,
            channel.display_name,
          ],
          id: channel.id,
          label: channel.display_name
        };
      });
    },
    processChannelResults(response) {
      this.cursor = '';
      if (response.data === null) {
        this.message = 'Sorry, no results for that query';
        return;
      }
      this.headers = ['Id', 'Name', 'Description'];
      this.rows = response.data.map(function(channel) {
        return {
          fields: [
            channel.id.channelId,
            channel.snippet.channelTitle,
            channel.snippet.description,
          ],
          id: channel.id.channelId,
          label: channel.snippet.channelTitle,
        };
      });
    },
    processYoutubeResults(response) {
      this.cursor = '';
      if (response.data === null) {
        this.message = 'Sorry, no results for that query';
        return;
      }
      this.headers = ['Id', 'Name', 'Description'];
      this.rows = response.data.map(function(channel) {
        return {
          fields: [
            channel.id.channelId,
            channel.snippet.channelTitle,
            channel.snippet.description,
          ],
          id: channel.id.channelId,
          label: channel.snippet.channelTitle,
        };
      });
    },
    processPopularResults(response) {
      this.cursor = response.data.pagination.cursor;
      if (response.data.data === null) {
        this.message = 'Sorry, no results for that query';
        return;
      }
      this.headers = ['Streamer', 'Game', 'Viewers'];
      let itemType = this.itemType;
      this.rows = response.data.data.map(function(stream) {
        let row = {
          fields: [
            stream.user_name,
            stream.game_name,
            stream.viewer_count
          ]
        };
        switch (itemType) {
          case 'game':
            row.id = stream.game_id;
            row.label = stream.game_name;
            break;
          case 'streamer':
            row.id = stream.user_id;
            row.label = stream.user_name;
            break;
        }
        return row;
      });
    },
    /* When selecting a new type, clear the values and search results */
    changeItemType(item) {
      this.clear();
      this.itemType = item.val;
      this.moreResults();
    }
  },
  mounted: function() {
    this.itemType = this.$el.parentNode.dataset['itemType'];

    /* Using the Sonata Select2 javascript hooks to update our itemType */
    let selector = document.querySelector("[data-topic-select='itemType']");
    if (selector) {
      this.itemType = $(selector).val();
      $(selector).on('change.select2', this.changeItemType);
    }

    this.moreResults();
  }
}
</script>
<style scoped>
tbody tr {
  cursor: pointer;
}
</style>
