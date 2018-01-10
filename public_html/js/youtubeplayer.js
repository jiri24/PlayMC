function onYouTubeIframeAPIReady() {
    player.video_player = new YT.Player('youtubeplayer', {
        height: '390',
        width: '640',
        videoId: player.track.youtube_code,
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerReady(event) {
    event.target.playVideo();
}

function onPlayerStateChange(event) {
    if (event.target.getPlayerState() == 0) {
        player.next(player);
    }
}