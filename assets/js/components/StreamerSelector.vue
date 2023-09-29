<template>
  <div v-if="itemType=='game'">
    <h3>Select Streamer of {{ gameName }}</h3>
    <div class="row">
      <div class="col-md-6 border-right">
        <div class="card card-topic-searchprimary card-tabs">
          <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
              <li class="nav-item active">
                <a class="nav-link active" id="custom-tabs-live-tab" data-toggle="pill" href="#custom-tabs-live" role="tab" aria-controls="custom-tabs-live" aria-selected="true">Live Streamer</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="custom-tabs-offline-tab" data-toggle="pill" href="#custom-tabs-offline" role="tab" aria-controls="custom-tabs-offline" aria-selected="false">Offline Streamer</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content" id="custom-tabs-one-tabContent">
              <div class="tab-pane active" id="custom-tabs-live" role="tabpanel" aria-labelledby="custom-tabs-live-tab">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input name="live-topic-search" class="form-control" v-model="searchValue" type="search"
                             placeholder="Enter a search term"></input>
                      <div class="well well-small">
                        <button @click="search()" type="button" class="btn btn-small btn-primary">Search</button>
                        <button @click="moreResults('before')" type="button" class="btn btn-small btn-primary">Previous Results</button>
                        <button @click="moreResults('after')" type="button" class="btn btn-small btn-primary">Next Results</button>
                        <button @click="clearSearch()" type="button" class="btn btn-small btn-primary">Reset Results</button>
                      </div>
                      <div v-if="message" class="well well-small">
                        <span class="message">{{ message }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <table id="live-streamer-table" class="tables_ui table table-bordered sonata-ba-list">
                      <tbody class="t_sortable">
                      <tr class="sonata-ba-list-field-header">
                        <th v-for="header in headers" class="sonata-ba-list-field-header">{{ header }}</th>
                      </tr>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
              <div class="tab-pane" id="custom-tabs-offline" role="tabpanel" aria-labelledby="custom-tabs-offline-tab">
                <div class="row">

                  <div class="col-md-12">
                    <div class="form-group">
                      <input name="offline-topic-search" class="form-control" v-model="offlineSearchValue" type="search"
                             placeholder="Enter a search term"></input>
                      <div class="well well-small">
                        <button @click="offlineSearch()" type="button" class="btn btn-small btn-primary">Search</button>
                        <button @click="clearOfflineSearch()" type="button" class="btn btn-small btn-primary">Reset Results</button>
                      </div>
                      <div v-if="message" class="well well-small">
                        <span class="message">{{ message }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <table id="offline-streamer-table" class="tables_ui table table-bordered sonata-ba-list">
                      <tbody class="t_sortable">
                      <tr class="sonata-ba-list-field-header">
                        <th v-for="header in headers" class="sonata-ba-list-field-header">{{ header }}</th>
                      </tr>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <h3>Black & Whitelist</h3>
        <table id="streamer-result-table" class="tables_ui table table-bordered sonata-ba-list">
          <tbody class="t_sortable">
          <tr class="sonata-ba-list-field-header">
            <th v-for="header in headers" class="sonata-ba-list-field-header">{{ header }}</th>
          </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
  <div v-else-if="itemType=='streamer'">

    <h3>Select Games</h3>
    <div class="row">
      <div class="col-md-6">
        <div class="col-md-12">
          <div class="form-group">
            <input name="game-search" class="form-control" v-model="gameSearchValue" type="search"
                   placeholder="Enter a search term"></input>
            <div class="well well-small">
              <button @click="game_search()" type="button" class="btn btn-small btn-primary">Search</button>
              <button @click="moreGameResults('before')" type="button" class="btn btn-small btn-primary">Previous Results</button>
              <button @click="moreGameResults('after')" type="button" class="btn btn-small btn-primary">Next Results</button>
              <button @click="clearGameSearch()" type="button" class="btn btn-small btn-primary">Reset Results</button>
            </div>
            <div v-if="message" class="well well-small">
              <span class="message">{{ message }}</span>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <table id="game-table" class="tables_ui table table-bordered table-striped table-hover sonata-ba-list">
            <tbody class="t_sortable">
            <tr class="sonata-ba-list-field-header">
              <th v-for="header in streamer_type_headers" class="sonata-ba-list-field-header">{{ header }}</th>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-md-6">
        <h3>Black & Whitelist</h3>
        <table id="game-result-table" class="tables_ui table table-bordered table-striped table-hover sonata-ba-list">
          <tbody class="t_sortable">
          <tr class="sonata-ba-list-field-header">
            <th v-for="header in streamer_type_headers" class="sonata-ba-list-field-header">{{ header }}</th>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
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
      streamer_type_headers: ['Streamer', 'Whitelist', 'Blacklist'],
      message: null,
      searchValue: null,
      offlineSearchValue: null,
      gameSearchValue: null,
      isSearching: false,
      isOldResultsUpdated: false,
      isOfflineSearching: false,
      isGameSearching: false,
      rows: [],
      offline_rows: [],
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
    /* Clicking on the search button */
    search: function() {
      if (!this.isSearching) {
        this.cursor = '';
        this.isSearching = true;
      }
      this.moreResults();
    },
    offlineSearch: function() {
      if (!this.isOfflineSearching) {
        this.isOfflineSearching = true;
      }
      this.moreOfflineResults();
    },
    game_search: function() {
      if (!this.isGameSearching) {
        this.isGameSearching = true;
      }
      this.moreGameResults();
    },
    /* Clearing search switches back to a popular search */
    clearSearch: function() {
      this.isSearching = false;
      this.searchValue = null;
      this.cursor = '';
      this.moreResults();
    },
    /* Clearing search switches back to a popular search */
    clearOfflineSearch: function() {
      this.isOfflineSearching = false;
      this.offlineSearchValue = null;
      $("#offline-streamer-table .streamer_row").remove();
    },
    clearGameSearch: function() {
      this.isGameSearching = false;
      this.gameSearchValue = null;
      $("#game-table .game_row").remove();
      this.moreGameResults();
    },
    /** == Call the API to get various kinds of results, then process them == **/
    getGamerStreamerResults: function(params) {
      var game_id = document.querySelector('input.topic-id').value;
      var game_name = document.querySelector('input.topic-label').value;
      this.gameId = game_id;
      this.gameName = game_name;
      if(this.gameId != '') {
        params['user_login'] = this.searchValue;
        this.callApi('/api/streams/'+this.gameId, params, this.processGameStreamerResults);
      }
    },
    getGamerOfflineStreamerResults: function(params) {
      if(this.offlineSearchValue != '') {
        this.callApi('/api/streams/offline/'+this.offlineSearchValue, params, this.processOfflineGameStreamerResults);
      }
    },
    getGameResults: function(params) {
      if(this.gameSearchValue != '') {
        this.callApi('/api/games/'+this.gameSearchValue, params, this.processGameResults);
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
      $("#live-streamer-table .streamer_row").remove();
      this.getGamerStreamerResults(params);
    },
    moreOfflineResults(direction) {
      this.message = null;
      let params = this.getPagerParams(direction);
      $("#offline-streamer-table .streamer_row").remove();
      this.getGamerOfflineStreamerResults(params);
    },
    moreGameResults(direction) {
      this.message = null;
      let params = this.getPagerParams(direction);
      $("#game-table .game_row").remove();
      this.getGameResults(params);
    },
    callApi(url, params, callback) {
      axios
        .get(url, { params: params })
        .then(response => callback(response))
    },
    processGameStreamerResults(response) {
      this.cursor = response.data.results_data.pagination.cursor;
      // if (response.data.results_data.data === null) {
      //   this.message = 'Sorry, no results for that query';
      //   return;
      // }
      this.headers = ['Streamer', 'Viewers', 'Blacklist'];
      $("#live-streamer-table .streamer_row").remove();
      if(response.data.results_data && response.data.results_data.data) {

        this.rows = response.data.results_data.data.map(function(stream) {

          var row_id = stream.id;
          var streamer = stream.user_name;
          var viewer_count = stream.viewer_count;
          var is_blacklisted = stream.is_blacklisted;
          var is_blacklisted_check = (is_blacklisted == 1)?'checked':'';

          var tr_data = `<tr class="streamer_row live_streamer">`;
          tr_data += `<td class="sonata-ba-list-field handle"><i class="fa fa-arrows"></i> `+streamer+`</td>
                      <td class="sonata-ba-list-field">`+viewer_count+`</td>
                      <td class="sonata-ba-list-field">
                        <input type="hidden" name="streamer_index[]" data-id="`+row_id+`" >
                        <input type="checkbox" name="is_blacklisted_`+row_id+`" `+is_blacklisted_check+` >
                        <input type="hidden" name="streamer_name_`+row_id+`" value="`+streamer+`">
                        <input type="hidden" name="viewer_`+row_id+`" value="`+viewer_count+`">
                        <input type="hidden" name="item_type_`+row_id+`" value="live_streamer">
                      </td>`;
          tr_data +=`</tr>`;

          $("#live-streamer-table tbody").append(tr_data);

        });

      }

      if(!this.isOldResultsUpdated) {
        $("#streamer-result-table .streamer_row").remove();
        if(response.data.old_selected_data) {
          this.rows = response.data.old_selected_data.map(function (stream) {

            var row_id = stream.id;
            var streamer = stream.user_name;
            var viewer_count = stream.viewer_count;
            var is_blacklisted = stream.is_blacklisted;
            var is_blacklisted_check = (is_blacklisted == 1) ? 'checked' : '';

            var item_type = stream.item_type;
            var item_type_class = '';
            if(item_type=='streamer') {
              item_type_class = 'live_streamer';
            } else {
              item_type_class = 'offline_streamer disabled_move';
            }
            var tr_data = `<tr class="streamer_row `+item_type_class+`">`;
            tr_data += `<td class="sonata-ba-list-field handle"><i class="fa fa-arrows"></i> ` + streamer + `</td>
                      <td class="sonata-ba-list-field">` + viewer_count + `</td>
                      <td class="sonata-ba-list-field">
                        <input type="hidden" name="streamer_index[]" value="` + row_id + `" data-id="` + row_id + `" >
                        <input type="checkbox" name="is_blacklisted_` + row_id + `" ` + is_blacklisted_check + ` >
                        <input type="hidden" name="streamer_name_` + row_id + `" value="` + streamer + `">
                        <input type="hidden" name="viewer_` + row_id + `" value="` + viewer_count + `">
                        <input type="hidden" name="item_type_`+row_id+`" value="`+item_type+`">
                      </td>`;
            tr_data += `</tr>`;

            $("#streamer-result-table tbody").append(tr_data);

          });
        }
        this.isOldResultsUpdated = true;
      }

    },
    processOfflineGameStreamerResults(response) {
      // if (response.data.results_data.data === null) {
      //   this.message = 'Sorry, no results for that query';
      //   return;
      // }
      this.headers = ['Streamer', 'Viewers', 'Blacklist'];
      $("#offline-streamer-table .streamer_row").remove();
      if(response.data.results_data && response.data.results_data.data) {

        this.offline_rows = response.data.results_data.data.map(function(stream) {

          var row_id = stream.id;
          var streamer = stream.login;
          var viewer_count = stream.view_count;

          var tr_data = `<tr class="streamer_row offline_streamer">`;
          tr_data += `<td class="sonata-ba-list-field handle"><i class="fa fa-arrows"></i> `+streamer+`</td>
                        <td class="sonata-ba-list-field">`+viewer_count+`</td>
                        <td class="sonata-ba-list-field">
                          <input type="hidden" name="streamer_index[]" data-id="`+row_id+`" >
                          <input type="checkbox" name="is_blacklisted_`+row_id+`" >
                          <input type="hidden" name="streamer_name_`+row_id+`" value="`+streamer+`">
                          <input type="hidden" name="viewer_`+row_id+`" value="`+viewer_count+`">
                          <input type="hidden" name="item_type_`+row_id+`" value="offline_streamer">
                        </td>`;
          tr_data +=`</tr>`;

          $("#offline-streamer-table tbody").append(tr_data);

        });
      }

    },
    processGameResults(response) {
      if(response.data.results_data.pagination && response.data.results_data.pagination.cursor) {
        this.cursor = response.data.results_data.pagination.cursor;
      }
      // if (response.data.results_data === null) {
      //   this.message = 'Sorry, no results for that query';
      //   return;
      // }
      $("#game-table .game_row").remove();
      if(response.data.results_data && response.data.results_data.data) {
        this.rows = response.data.results_data.data.map(function (stream) {

          var row_id = stream.id;
          var streamer = stream.name;

          var tr_data = `<tr class="game_row">`;
          tr_data += `<td class="sonata-ba-list-field handle"><i class="fa fa-arrows"></i> ` + streamer + `</td>
                    <td class="sonata-ba-list-field">
                     <input type="checkbox" name="is_whitelisted_` + row_id + `" >
                    </td>
                    <td class="sonata-ba-list-field">
                      <input type="hidden" name="game_index[]" data-id="` + row_id + `" >
                      <input type="checkbox" name="is_blacklisted_` + row_id + `" >
                      <input type="hidden" name="streamer_name_` + row_id + `" value="` + streamer + `">
                    </td>`;
          tr_data += `</tr>`;

          $("#game-table tbody").append(tr_data);
        });
      }

      if(!this.isOldResultsUpdated) {
        $("#game-result-table .game_row").remove();

        if (response.data.old_selected_data) {
          this.rows = response.data.old_selected_data.map(function (stream) {

            var row_id = stream.id;
            var streamer = stream.name;
            var is_blacklisted = stream.is_blacklisted;
            var is_whitelisted = stream.is_whitelisted;
            var is_blacklisted_check = (is_blacklisted == 1) ? 'checked' : '';
            var is_whitelisted_check = (is_whitelisted == 1) ? 'checked' : '';

            var tr_data = `<tr class="game_row">`;
            tr_data += `<td class="sonata-ba-list-field handle"><i class="fa fa-arrows"></i> ` + streamer + `</td>
                    <td class="sonata-ba-list-field">
                     <input type="checkbox" name="is_whitelisted_` + row_id + `" ` + is_whitelisted_check + ` >
                    </td>
                    <td class="sonata-ba-list-field">
                      <input type="hidden" name="game_index[]" value="` + row_id + `" data-id="` + row_id + `" >
                      <input type="checkbox" name="is_blacklisted_` + row_id + `" ` + is_blacklisted_check + ` >
                      <input type="hidden" name="streamer_name_` + row_id + `" value="` + streamer + `">
                    </td>`;
            tr_data += `</tr>`;

            $("#game-result-table tbody").append(tr_data);
          });
        }

        this.isOldResultsUpdated = true;
      }
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
      this.isOldResultsUpdated = false;
      $("#live-streamer-table .streamer_row").remove();
      $("#offline-streamer-table .streamer_row").remove();
      $("#streamer-result-table .streamer_row").remove();
      $("#game-result-table .game_row").remove();
      let selector = document.querySelector("[data-topic-select='itemType']");
      if (selector) {
        this.itemType = $(selector).val();
      }
      if(this.itemType == 'streamer') {
          this.moreGameResults();
      }
    });

  },
  mounted: function() {
    let selector = document.querySelector("[data-topic-select='itemType']");
    if (selector) {
      this.itemType = $(selector).val();
    }
    if(this.itemType == 'game') {
      this.moreResults();
      this.moreOfflineResults();
    } else if(this.itemType == 'streamer') {
      this.moreGameResults();
    }

  },
  updated: function () {
    let selector = document.querySelector("[data-topic-select='itemType']");
    if (selector) {
      this.itemType = $(selector).val();
    }
    if(this.itemType == 'game') {

      var $tabs = $('#streamer-result-table')
      $("tbody.t_sortable").sortable({
        connectWith: ".t_sortable",
        items: "> tr:not(:first)",
        appendTo: $tabs,
        helper:"clone",
        zIndex: 999990
      }).disableSelection();

      $("#live-streamer-table tbody.t_sortable" ).sortable({
        stop: function (e, ui) {
          $('#live-streamer-table input[name="streamer_index[]"]').each(function(i){
            $(this).val('');
          });

          $('#streamer-result-table input[name="streamer_index[]"]').each(function(i){
            let streamer_id = $(this).data('id');
            $(this).val(streamer_id);
          });
        }
      });

      $("#streamer-result-table tbody.t_sortable" ).sortable({
        stop: function (e, ui) {
          $('#live-streamer-table input[name="streamer_index[]"]').each(function(i){
            $(this).val('');
          });

          $('#offline-streamer-table input[name="streamer_index[]"]').each(function(i){
            $(this).val('');
          });
        }
      });

      $("#offline-streamer-table tbody.t_sortable" ).sortable({
        stop: function (e, ui) {
          $('#offline-streamer-table input[name="streamer_index[]"]').each(function(i){
            $(this).val('');
          });

          $('#streamer-result-table input[name="streamer_index[]"]').each(function(i){
            let streamer_id = $(this).data('id');
            $(this).val(streamer_id);
          });
        }
      });

    } else if(this.itemType == 'streamer') {

      var $game_tabs = $('#game-result-table')
      $("tbody.t_sortable").sortable({
        connectWith: ".t_sortable",
        items: "> tr:not(:first)",
        appendTo: $game_tabs,
        helper: "clone",
        zIndex: 999990
      }).disableSelection();

      $("#game-table tbody.t_sortable" ).sortable({
        stop: function (e, ui) {
          $('#game-table input[name="game_index[]"]').each(function(i){
            $(this).val('');
          });

          $('#game-result-table input[name="game_index[]"]').each(function(i){
            let streamer_id = $(this).data('id');
            $(this).val(streamer_id);
          });
        }
      });

      $("#game-result-table tbody.t_sortable" ).sortable({
        stop: function (e, ui) {
          $('#game-table input[name="game_index[]"]').each(function(i){
            $(this).val('');
          });
        }
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
