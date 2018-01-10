var Player = function (type) {
    // Typ přehrávače
    // Youtube - true
    // Spotify - false
    this.type = type;
    document.querySelector("#videoplayer").style.display = "none";
}

// Inicializace proměnných
Player.prototype.playing = false;
Player.prototype.video_player = null;
Player.prototype.audio_player = null;
Player.prototype.track = null;
Player.prototype.genre = 0;
Player.prototype.year = "";
Player.prototype.mode = 2;
Player.prototype.national = false;

// Přehraje audio
Player.prototype.playAudio = function (song) {
    if (!this.audio_player) {
        // Pokud přehrávač neexistuje, vytvoř ho
        player = this;
        this.audio_player = soundManager.createSound({
            url: song,
            onfinish: function () {
                player.next(player);
            },
        });
        this.playing = true;
        this.audio_player.play();
    } else {
        // Přehraj písníčku
        this.audio_player.play({
            url: song,
        });
    }
    // Změň tlačítko
    var button = document.querySelector("#play");
    button.className = "fa fa-lg fa-pause";
};

// Přehraj video
Player.prototype.playVideo = function (song) {
    if (!this.video_player) {
        // Pokud přehrávač neexistuje, vytvoř ho
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    } else {
        // Přehraj video
        this.video_player.loadVideoById(song);
    }
    // Změň tlačítko
    var button = document.querySelector("#play");
    button.className = "fa fa-lg fa-pause";
};

// Hraj nebo zastav
Player.prototype.play = function () {
    if (this.video_player || this.audio_player) {
        if (this.playing) {
            // Pozastav přehrávání
            document.querySelector("#play").className = "fa fa-lg fa-play";
            this.playing = false;
            if (this.type) {
                // Youtube
                this.video_player.pauseVideo();
            } else {
                // Spotify
                this.audio_player.pause();
            }
        } else {
            // Pokračuj v přehrávání
            document.querySelector("#play").className = "fa fa-lg fa-pause";
            this.playing = true;
            if (this.type) {
                // Youtube
                this.video_player.playVideo();
            } else {
                // Spotify
                this.audio_player.play();
            }
        }
    }
};

// Přepne přehrávač
Player.prototype.switchPlayer = function (clicked) {
    if (!this.type && clicked == 'youtube') {
        // Zapni Youtube
        document.querySelector("#videoplayer").style.display = "block";
        document.querySelector("#spotify").className = "btn btn-dark";
        document.querySelector("#youtube").className = "btn btn-danger";
        this.type = true;
        if (this.track) {
            this.audio_player.stop();
            this.playVideo(this.track.youtube_code);
        }
    }
    if (this.type && clicked == 'spotify')
    {
        // Zapni Spotify
        document.querySelector("#videoplayer").style.display = "none";
        document.querySelector("#youtube").className = "btn btn-dark";
        document.querySelector("#spotify").className = "btn btn-success";
        this.type = false;
        if (this.track) {
            this.video_player.stopVideo();
            this.playAudio(this.track.preview_url);
        }
    }
};

// Načte další skladbu
Player.prototype.next = function (player) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var track = JSON.parse(this.responseText);
            player.track = track;
            if (player.type) {
                // Youtube
                player.playVideo(track.youtube_code);
            } else {
                // Spotify
                player.playAudio(track.preview_url);
            }
            if (player.track.like) {
                document.querySelector("#like").className = "btn btn-light";
            } else {
                document.querySelector("#like").className = "btn btn-dark";
            }
            var title = document.querySelector("#track-title");
            title.innerHTML = track.full_title;
        }
    };
    var url = "./api.php?nextSong&mode=" + this.mode + "&genre=" + this.genre + ((this.year != "") ? "&year=" + this.year : "&year=") + ((this.national) ? "&national" : "");
    xhttp.open("GET", url, true);
    xhttp.send();
};

// Přehraj žánr
Player.prototype.playGenre = function (genre) {
    this.genre = genre;
    this.next(this);
};

// Přehraj rok
Player.prototype.playYear = function (year) {
    this.year = year;
    this.next(this);
};

// Nastav mód
Player.prototype.setMode = function (mode) {
    document.querySelector("#mode" + this.mode).className = "btn btn-dark";
    this.mode = mode;
    document.querySelector("#mode" + this.mode).className = "btn btn-light";
    this.next(this);
};

// Nastaví mód
Player.prototype.setNational = function (national) {
    this.national = national;
    this.next(this);
};

// To se mi líbí
Player.prototype.like = function () {
    if (this.track) {
        if (!this.track.like) {
            this.track.like = true;
            document.querySelector("#like").className = "btn btn-light";
        } else {
            this.track.like = false;
            document.querySelector("#like").className = "btn btn-dark";
        }
        var http = new XMLHttpRequest();
        var url = "./api.php?like";
        var params = "like=" + this.track.track_id;
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function () {//Call a function when the state changes.
            if (http.readyState == 4 && http.status == 200) {
            }
        }
        http.send(params);
    }
};

// To se mi líbí
Player.prototype.dislike = function () {
    if (this.track) {
        var http = new XMLHttpRequest();
        var url = "./api.php?dislike";
        var params = "dislike=" + this.track.track_id;
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function () {//Call a function when the state changes.
            if (http.readyState == 4 && http.status == 200) {
            }
        }
        http.send(params);
        this.next(this);
    }
};