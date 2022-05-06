$(document).ready(function(){
    $('.video-id').parent().parent().addClass('hide');
    $('.playlist-id').parent().parent().addClass('hide');

    var selector = document.querySelector("[data-topic-select='itemType']");

    $(selector).on('change.select2', function(){
        var title = $(this).val();
        
        if(typeof(title) != 'undefined'){
            if(title == 'twitch-video'){
                $('.video-id').val('https://player.twitch.tv/?video=');
                $('.video-id').parent().parent().removeClass('hide');
                $('.playlist-id').parent().parent().addClass('hide');
            }
            else if(title == 'twitch-playlist'){
                $('.playlist-id').val('https://player.twitch.tv/?collection=');
                $('.video-id').parent().parent().addClass('hide');
                $('.playlist-id').parent().parent().removeClass('hide');
            }
            else if(title == 'youtube-video'){
                $('.video-id').val('https://www.googleapis.com/youtube/v3//videos?key=');
                $('.video-id').parent().parent().removeClass('hide');
                $('.playlist-id').parent().parent().addClass('hide');
            }
            else if(title == 'youtube-playlist'){
                $('.playlist-id').val('https://www.googleapis.com/youtube/v3/playlists?key=');
                $('.video-id').parent().parent().addClass('hide');
                $('.playlist-id').parent().parent().removeClass('hide');
            }
            else{
                $('.video-id').val('');
                $('.playlist-id').val('');
                $('.video-id').parent().parent().addClass('hide');
                $('.playlist-id').parent().parent().addClass('hide');
            }
        }
    });
});   
