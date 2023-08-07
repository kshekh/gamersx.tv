$(document).ready(function(){
  var videoId = $('.video-id').val();
  var playlistId = $('.playlist-id').val();

  if(videoId == '')
    $('.video-id').parent().parent().addClass('hide');

  if(playlistId == '')
    $('.playlist-id').parent().parent().addClass('hide');

  var selector = document.querySelector("[data-topic-select='itemType']");

  $(selector).on('change.select2', function(){
    var title = $(this).val();

    if(typeof(title) != 'undefined'){
      if(title == 'twitch_video'){
        $('.video-id').val('');
        $('.video-id').attr('placeholder', 'https://player.twitch.tv/?video=');
        $('.video-id').parent().parent().removeClass('hide');
        $('.playlist-id').parent().parent().addClass('hide');
      }
      else if(title == 'twitch_playlist'){
        $('.playlist-id').val('');
        $('.playlist-id').attr('placeholder', 'https://player.twitch.tv/?collection=');
        $('.video-id').parent().parent().addClass('hide');
        $('.playlist-id').parent().parent().removeClass('hide');
      }
      else if(title == 'youtube_video'){
        $('.video-id').val('');
        $('.video-id').attr('placeholder', 'https://www.googleapis.com/youtube/v3/videos?key=');
        $('.video-id').parent().parent().removeClass('hide');
        $('.playlist-id').parent().parent().addClass('hide');
      }
      else if(title == 'youtube_playlist'){
        $('.playlist-id').val('');
        $('.playlist-id').attr('placeholder', 'https://www.googleapis.com/youtube/v3/playlists?key=');
        $('.video-id').parent().parent().addClass('hide');
        $('.playlist-id').parent().parent().removeClass('hide');
      }
      else{
        $('.video-id').val('');
        $('.playlist-id').val('');
        $('.video-id').attr('placeholder', '');
        $('.playlist-id').attr('placeholder', '');
        $('.video-id').parent().parent().addClass('hide');
        $('.playlist-id').parent().parent().addClass('hide');
      }
    }
  });

  $('.video-id').on('blur', function(){
    var videoUrl = $('.video-id').val();
    if(videoUrl && typeof(videoUrl) != 'undefined'){
      let videoUrlSplit = videoUrl.split('?')[1];

      const videoUrlParams = new URLSearchParams(videoUrlSplit);
      const videoId = videoUrlParams.get('video');

      if(videoId && typeof(videoId) != 'undefined'){
        $('.video-id').val(videoId);
      }

      const videoKey = videoUrlParams.get('key');

      if(videoKey && typeof(videoKey) != 'undefined'){
        $('.video-id').val(videoKey);
      }
    }
  });

  $('.playlist-id').on('blur', function(){
    var playlistUrl = $('.playlist-id').val();
    if(playlistUrl && playlistUrl != 'undefined'){
      let playlistUrlSplit = playlistUrl.split('?')[1];

      const playlistUrlParams = new URLSearchParams(playlistUrlSplit);
      const collectionId = playlistUrlParams.get('collection');

      if(collectionId && typeof(collectionId) != 'undefined'){
        $('.playlist-id').val(collectionId);
      }

      const playlistKey = playlistUrlParams.get('key');

      if(playlistKey && typeof(playlistKey) != 'undefined'){
        $('.playlist-id').val(playlistKey);
      }
    }
  });
});
