<?php

require_once './../config.php';

class Recommender {

    private $system;
    private $popularityBreak = 50;
    private $top = 200;
    private $trackLimit = 50;
    private $artistLimit = 20;
    private $divideLimit = 10;

    public function __construct(System $system) {
        $this->system = $system;
        if ($this->system->getAuth()->isLogin()) {
            switch ($this->system->getURL()->getArg(0)) {
                case "nextSong":
                    $this->nextSong();
                    break;
                case "like":
                    $this->like();
                    break;
                case "dislike":
                    $this->dislike();
                    break;
                case "search":
                    $this->search();
                    break;
                case "searchArtist":
                    $this->searchArtist();
                    break;
                case "searchMaster":
                    $this->searchMaster();
                    break;
                case "searchTrack":
                    $this->searchTrack();
                    break;
                case "getWhiteList":
                    $this->getWhiteList();
                    break;
                case "getBlackList":
                    $this->getBlackList();
                    break;
                default :
                    $system->getTemplate()->error();
                    break;
            }
        } else {
            echo(json_encode(array('error' => 'A user is not login.')));
        }
    }

    private function like() {
        if (isset($_POST['like'])) {
            $insert = array($this->system->getAuth()->getUser()->getID(), htmlspecialchars($_POST['like']));
            $data = $this->system->getDB()->query("SELECT * FROM white_list WHERE user_id=? AND track_id=?", $insert);
            if (count($data) == 0) {
                // Přidej like
                $this->system->getDB()->insert("INSERT INTO white_list(user_id,track_id) VALUES(?,?)", $insert);
                echo(json_encode(array("status" => "liked")));
            } else {
                // Odeber like
                $this->system->getDB()->query("DELETE FROM white_list WHERE id=?", array($data[0]['id']));
                echo(json_encode(array("status" => "disliked")));
            }
        } else {
            echo(json_encode(array("status" => "error")));
        }
    }

    private function dislike() {
        if (isset($_POST['dislike'])) {
            $insert = array($this->system->getAuth()->getUser()->getID(), htmlspecialchars($_POST['dislike']));
            $data = $this->system->getDB()->query("SELECT * FROM black_list WHERE user_id=? AND track_id=?", $insert);
            if (count($data) == 0) {
                // Přidej dislike
                $this->system->getDB()->insert("INSERT INTO black_list(user_id,track_id) VALUES(?,?)", $insert);
                echo(json_encode(array("status" => "disliked")));
            } else {
                // Odeber dislike
                $this->system->getDB()->query("DELETE FROM black_list WHERE id=?", array($data[0]['id']));
                echo(json_encode(array("status" => "disliked")));
            }
        } else {
            echo(json_encode(array("status" => "error")));
        }
    }

    // Doporučení
    private function nextSong() {
        switch ($this->system->getURL()->getValue("mode")) {
            case 1 :
                $num = $this->system->getRandom()->exponentialDistribution(3);
                switch ($num) {
                    case 0:
                        $this->recommendNewOrUnknownPopular();
                        break;
                    case 1:
                        $this->potentionalKnown();
                        break;
                    case 2:
                        $this->favourite();
                        break;
                }
                break;
            case 2 :
                $index = $this->indexOfPopularity() / 100;
                if ($this->system->getRandom()->bernoulliDistribution($index) == 1) {
                    $this->recommendNewOrUnknownPopular();
                } else {
                    $this->recommendNewOrUnknownUnpopular();
                }
                break;
            case 3 :
                $num = $this->system->getRandom()->exponentialDistribution(3);
                switch ($num) {
                    case 0:
                        $this->recommendNewOrUnknownUnpopular();
                        break;
                    case 1:
                        $this->recommendNewOrUnknownUnpopular();
                        break;
                    case 2:
                        $this->favourite();
                        break;
                }
                break;
            default:
                echo(json_encode(array('error' => 'Unknown mode.')));
        }
    }

    // Oblíbené
    private function favourite() {
        $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=? ORDER BY time DESC LIMIT " . $this->trackLimit;
        $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=? ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
        $tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
        $white_list = "SELECT track_id FROM white_list WHERE user_id=?";
        $except = "(($white_list) EXCEPT ($played_tracks) EXCEPT $tracks)";
        $sql = "SELECT t.* FROM ($except) e, tracks_selection t WHERE e.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t.");
        $userID = $this->system->getAuth()->getUser()->getID();
        $query = $this->system->getDB()->simpleQuery($sql, array($userID, $userID, $userID));
        if ($query->rowCount() != 0) {
            $data = null;
            $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            for ($i = 0; $i <= $num; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $this->printTrack($data);
        } else {
            $this->potentionalKnown();
        }
    }

    // Potenciálně známé
    private function potentionalKnown() {
        if ($this->system->getRandom()->uniformDistribution(0, 1) == 0) {
            $this->favouriteArtist();
        } else {
            $this->favouriteAlbum();
        }
    }

    // Oblíbený interpret
    private function favouriteArtist() {
        $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->trackLimit;
        $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
        $played_artists_tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
        $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
        $artists = "SELECT ma.artist_name FROM white_list w, tracks_selection t, masters_artists ma WHERE w.user_id=:user AND w.track_id=t.track_id AND t.master_id=ma.master_id GROUP BY ma.artist_name";
        $selected_tracks = "SELECT t.track_id FROM ($artists) a, tracks_selection t, masters_artists ma WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
        $except = "((($selected_tracks) EXCEPT ($played_tracks)) EXCEPT ($played_artists_tracks)) EXCEPT ($white_list)";
        $sql = "SELECT t.* FROM ($except) e, tracks_selection t WHERE e.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t.");
        $userID = $this->system->getAuth()->getUser()->getID();
        $query = $this->system->getDB()->simpleQuery($sql, array(":user" => $userID));
        if ($query->rowCount() != 0) {
            $data = null;
            $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            for ($i = 0; $i <= $num; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $this->printTrack($data);
        } else {
            $this->newOrUnknownPopular();
        }
    }

    // Oblíbené album
    private function favouriteAlbum() {
        $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->trackLimit;
        $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
        $played_artists_tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
        $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
        $albums = "SELECT t.release_id FROM tracks_selection t, white_list w WHERE w.user_id=:user AND w.track_id=t.track_id GROUP BY t.release_id";
        $album_tracks = "SELECT t.track_id FROM ($albums) a, tracks_selection t WHERE a.release_id=t.release_id";
        $except = "((($album_tracks) EXCEPT ($played_tracks)) EXCEPT ($played_artists_tracks)) EXCEPT ($white_list)";
        $sql = "SELECT t.* FROM ($except) e, tracks_selection t WHERE e.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t.");
        $userID = $this->system->getAuth()->getUser()->getID();
        $query = $this->system->getDB()->simpleQuery($sql, array(":user" => $userID));
        if ($query->rowCount() != 0) {
            $data = null;
            $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            for ($i = 0; $i <= $num; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $this->printTrack($data);
        } else {
            $this->favouriteArtist();
        }
    }

    // Doporuční novou nebo neznámou skladbu - populární
    private function recommendNewOrUnknownPopular() {
        // Výběr písničky, ze které budeme vycházet
        $userID = $this->system->getAuth()->getUser()->getID();
        $sql = "SELECT w.track_id FROM white_list w, tracks_selection t WHERE w.user_id=:user AND w.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t.");
        $query = $this->system->getDB()->simpleQuery($sql, array(":user" => $userID));
        if ($query->rowCount() != 0) {
            // Zjištění limitu
            $sql_limit = "SELECT popularity FROM tracks_selection WHERE 1=1" . $this->getGenre() . $this->getYear() . " ORDER BY popularity DESC";
            $query = $this->system->getDB()->simpleQuery($sql_limit);
            // Vypočítáme limit
            $limit = round($query->rowCount() / $this->divideLimit);
            for ($i = 0; $i <= $limit; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $popularity = $data['popularity'];
            // Výber blízké písničky
            $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
            $played_artists_tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
            $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
            $union = "($played_artists_tracks) UNION ($black_list)";
            $selection = "SELECT track_id FROM tracks_selection WHERE popularity>=:limit AND track_id NOT IN ($union)" . $this->getGenre() . $this->getYear();
            $sql = "SELECT t.* FROM ($selection) e, tracks_distance d, tracks_selection t, white_list w WHERE "
                    . "w.user_id=:user AND d.track_id1=w.track_id AND d.track_id2=e.track_id AND d.track_id2=t.track_id";
            $query = $this->system->getDB()->simpleQuery($sql, array(":user" => $userID, ":limit" => $popularity));
            if ($query->rowCount() != 0) {
                $data = $this->randomSelection($query);
                $this->printTrack($data);
            } else {
                $this->newOrUnknownPopular();
            }
        } else {
            $this->newOrUnknownPopular();
        }
    }

    // Doporuční novou nebo neznámou skladbu - nepopulární
    private function recommendNewOrUnknownUnpopular() {
        // Výběr písničky, ze které budeme vycházet
        $userID = $this->system->getAuth()->getUser()->getID();
        $sql = "SELECT w.track_id FROM white_list w, tracks_selection t WHERE w.user_id=:user AND w.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t.");
        $query = $this->system->getDB()->simpleQuery($sql, array(":user" => $userID));
        if ($query->rowCount() != 0) {
            // Zjištění limitu
            $sql_limit = "SELECT popularity FROM tracks_selection WHERE 1=1" . $this->getGenre() . $this->getYear() . " ORDER BY popularity DESC";
            $query = $this->system->getDB()->simpleQuery($sql_limit);
            // Vypočítáme limit
            $limit = round($query->rowCount() / $this->divideLimit);
            for ($i = 0; $i <= $limit; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $popularity = $data['popularity'];
            // Výber blízké písničky
            $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
            $played_artists_tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
            $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
            $union = "($played_artists_tracks) UNION ($black_list)";
            $selection = "SELECT track_id FROM tracks_selection WHERE popularity<:limit AND track_id NOT IN ($union)" . $this->getGenre() . $this->getYear();
            $sql = "SELECT t.* FROM ($selection) e, tracks_distance d, tracks_selection t, white_list w WHERE "
                    . "w.user_id=:user AND d.track_id1=w.track_id AND d.track_id2=e.track_id AND d.track_id2=t.track_id";
            $query = $this->system->getDB()->simpleQuery($sql, array(":user" => $userID, ":limit" => $popularity));
            if ($query->rowCount() != 0) {
                $data = $this->randomSelection($query);
                $this->printTrack($data);
            } else {
                $this->newOrUnknownUnpopular();
            }
        } else {
            $this->newOrUnknownUnpopular();
        }
    }

    // Nové/neznámé - populární
    private function newOrUnknownPopular() {
        // Zjištění limitu
        $sql_limit = "SELECT popularity FROM tracks_selection WHERE 1=1" . $this->getGenre() . $this->getYear() . " ORDER BY popularity DESC";
        $query = $this->system->getDB()->simpleQuery($sql_limit);
        // Vypočítáme limit
        $limit = round($query->rowCount() / $this->divideLimit);
        for ($i = 0; $i <= $limit; $i++) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }
        $popularity = $data['popularity'];
        // Získá skladby
        $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
        $played_artists_tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
        $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
        $union = "($played_artists_tracks) UNION ($black_list)";
        $sql = "SELECT * FROM tracks_selection WHERE popularity>=:limit AND track_id NOT IN ($union)" . $this->getGenre() . $this->getYear();
        $query = $this->system->getDB()->simpleQuery($sql, array(":limit" => $popularity, ":user" => $this->system->getAuth()->getUser()->getID()));
        $data = null;
        $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
        for ($i = 0; $i <= $num; $i++) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }
        $this->printTrack($data);
    }

    // Nové/neznámé - nepopulární
    private function newOrUnknownUnpopular() {
        // Zjištění limitu
        $sql_limit = "SELECT popularity FROM tracks_selection WHERE 1=1" . $this->getGenre() . $this->getYear() . " ORDER BY popularity DESC";
        $query = $this->system->getDB()->simpleQuery($sql_limit);
        // Vypočítáme limit
        $limit = round($query->rowCount() / $this->divideLimit);
        for ($i = 0; $i <= $limit; $i++) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }
        $popularity = $data['popularity'];
        // Získá skladby
        $played_artists = "SELECT a.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT " . $this->artistLimit . ") p, tracks_selection t, masters_artists a WHERE p.track_id=t.track_id AND t.master_id=a.master_id GROUP BY a.artist_name";
        $played_artists_tracks = "SELECT track_id FROM ($played_artists) a, masters_artists ma, tracks_selection t WHERE a.artist_name=ma.artist_name AND ma.master_id=t.master_id";
        $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
        $union = "($played_artists_tracks) UNION ($black_list)";
        $sql = "SELECT * FROM tracks_selection WHERE popularity<:limit AND track_id NOT IN ($union)" . $this->getGenre() . $this->getYear();
        $query = $this->system->getDB()->simpleQuery($sql, array(":limit" => $popularity, ":user" => $this->system->getAuth()->getUser()->getID()));
        $data = null;
        $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
        for ($i = 0; $i <= $num; $i++) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }
        $this->printTrack($data);
    }

    // Sestaví autory a název písničky
    private function findTitle($track) {
        $data = $this->system->getDB()->query("select a.artist_name from track t, master m, masters_artists a where t.track_id=? and m.main_release=t.release_id and m.id=a.master_id", array($track['track_id']));
        $name = $data[0]['artist_name'];
        for ($k = 0; $k < 100; $k++) {
            $name = str_replace(" ($k)", "", $name);
        }
        return $name . " - " . $track['title'];
    }

    // Najde Youtube video
    private function findYoutubeVideo($title) {
        $url = "https://www.googleapis.com/youtube/v3/search?q=" . urlencode($title) . "&maxResults=1&key=" . YOUTUBE_API_KEY . "&part=snippet";
        $result = file_get_contents($url);
        $result = json_decode($result, true);
        return $result['items'][0]['id']['videoId'];
    }

    // Vrať na výstup skladbu
    private function printTrack($data) {
        $this->playedTrack($data);
        $like = $this->system->getDB()->query("SELECT * FROM white_list WHERE user_id=? AND track_id=?", array($this->system->getAuth()->getUser()->getID(), $data['track_id']));
        $fullTitle = $this->findTitle($data);
        $json = array(
            'track_id' => $data['track_id'],
            'title' => $data['title'],
            'spotify_id' => $data['spotify_id'],
            'preview_url' => $data['preview_url'],
            'full_title' => $fullTitle . " (" . $data['year'] . "): " . $data['genres'],
            'youtube_code' => $this->findYoutubeVideo($fullTitle),
            'like' => (count($like) == 1) ? true : false);
        echo(json_encode($json));
    }

    // Přidá skladbu do přehrátých
    private function playedTrack($data) {
        $this->system->getDB()->insert("INSERT INTO plays_history(user_id, track_id, time) VALUES(?,?,NOW())", array($this->system->getAuth()->getUser()->getID(), $data['track_id']));
    }

    // Nastavit žánr
    private function getGenre($prefix = "") {
        if ($this->system->getURL()->getArg(2) == "genre") {
            $genreID = $this->system->getURL()->getValue("genre");
            $genre = $this->system->getDB()->query("SELECT * FROM genre WHERE id=?", array($genreID));
            if (count($genre) != 0) {
                return " AND " . $prefix . "genres LIKE '%" . $genre[0]['name'] . "%'";
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    // Nastaví rok
    private function getYear($prefix = "") {
        if ($this->system->getURL()->getArg(3) == "year") {
            $yearID = $this->system->getURL()->getValue("year");
            switch ($yearID) {
                case "10s":
                    return " AND " . $prefix . "year>=2010";
                    break;
                case "00s":
                    return " AND " . $prefix . "year>=2000 AND " . $prefix . "year<2010";
                    break;
                case "90s":
                    return " AND " . $prefix . "year>=1990 AND " . $prefix . "year<2000";
                    break;
                case "80s":
                    return " AND " . $prefix . "year>=1980 AND " . $prefix . "year<1990";
                    break;
                case "70s":
                    return " AND " . $prefix . "year>=1970 AND " . $prefix . "year<1980";
                    break;
                case "60s":
                    return " AND " . $prefix . "year>=1960 AND " . $prefix . "year<1970";
                    break;
                case "50s":
                    return " AND " . $prefix . "year>=1950 AND " . $prefix . "year<1960";
                    break;
            }
        } else {
            return "";
        }
    }

    // Provede náhodný výběr z dotazu
    private function randomSelection($query) {
        $data = null;
        $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
        for ($i = 0; $i <= $num; $i++) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }
        return $data;
    }

    // Vyhledat v databázi
    private function search() {
        if (isset($_POST['query'])) {
            $query = htmlspecialchars($_POST['query']);
            $data = array();
            $data['artist'] = $this->system->getDB()->simpleQuery("SELECT id, name FROM artist WHERE name @@ plainto_tsquery('english',?) LIMIT 50", array($query))->fetchAll(PDO::FETCH_ASSOC);
            $data['master'] = $this->system->getDB()->simpleQuery("SELECT id, title FROM master WHERE title @@ plainto_tsquery('english',?) LIMIT 50", array($query))->fetchAll(PDO::FETCH_ASSOC);
            $data['track'] = $this->system->getDB()->simpleQuery("SELECT track_id AS id, title FROM track WHERE title @@ plainto_tsquery('english',?) LIMIT 50", array($query))->fetchAll(PDO::FETCH_ASSOC);
            echo(json_encode($data));
        }
    }

    // Vyhledat umělce podle ID
    private function searchArtist() {
        $id = intval($_POST['query']);
        $data = $this->system->getDB()->query("SELECT * FROM artist WHERE id=?", array($id));
        echo(json_encode($data[0]));
    }

    // Vyhledat album podle ID
    private function searchMaster() {
        $id = intval($_POST['query']);
        $data = $this->system->getDB()->query("SELECT * FROM master WHERE id=?", array($id));
        echo(json_encode($data[0]));
    }

    // Vyhledat skladbu podle ID
    private function searchTrack() {
        $id = intval($_POST['query']);
        $data = $this->system->getDB()->query("SELECT * FROM track WHERE track_id=?", array($id));
        echo(json_encode($data[0]));
    }

    // Získá seznam oblíbených skladeb
    private function getWhiteList() {
        $data = $this->system->getDB()->simpleQuery("SELECT t.* FROM white_list w, tracks_selection t WHERE w.track_id=t.track_id AND w.user_id=?", array($this->system->getAuth()->getUser()->getID()))->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($data));
    }

    // Získá seznam neoblíbených skladeb
    private function getBlackList() {
        $data = $this->system->getDB()->simpleQuery("SELECT t.* FROM black_list b, tracks_selection t WHERE b.track_id=t.track_id AND b.user_id=?", array($this->system->getAuth()->getUser()->getID()))->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($data));
    }

    // Výpočet indexu popularity oblíbených interpretů
    private function indexOfPopularity() {
        $sql = "SELECT SUM(a.popularity * ua.count) / SUM(ua.count) AS index FROM " .
                "(SELECT m.artist_name, COUNT(m.artist_name) AS count FROM white_list w, tracks_selection t, masters_artists m WHERE user_id=? AND w.track_id=t.track_id AND t.master_id=m.master_id GROUP BY m.artist_name) ua, artist a " .
                "WHERE ua.artist_name=a.name";
        return $this->system->getDB()->query($sql, array($this->system->getAuth()->getUser()->getID()))[0]['index'];
    }

}
