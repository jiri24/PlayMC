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
                case "getArtist":
                    $this->getArtist();
                    break;
                case "getAlbum":
                    $this->getAlbum();
                    break;
                case "getWhiteList":
                    $this->getWhiteList();
                    break;
                case "getBlackList":
                    $this->getBlackList();
                    break;
                case "getGenres":
                    $this->getGenres();
                    break;
                case "getYears":
                    $this->getYears();
                    break;
                case "getTrack":
                    $this->getTrack();
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
                        $this->newOrUnknown(true);
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
                    $num = $this->system->getRandom()->exponentialDistribution(3);
                    switch ($num) {
                        case 0:
                            $this->newOrUnknown(true);
                            break;
                        case 1:
                            $this->potentionalKnown();
                            break;
                        case 2:
                            $this->favourite();
                            break;
                    }
                } else {
                    $num = $this->system->getRandom()->exponentialDistribution(3);
                    switch ($num) {
                        case 0:
                            $this->newOrUnknown(false);
                            break;
                        case 1:
                            $this->newOrUnknown(false);
                            break;
                        case 2:
                            $this->favourite();
                            break;
                    }
                }
                break;
            case 3 :
                $num = $this->system->getRandom()->exponentialDistribution(3);
                switch ($num) {
                    case 0:
                        $this->newOrUnknown(false);
                        break;
                    case 1:
                        $this->newOrUnknown(false);
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
        $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
        $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
        $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
        $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
        $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
        $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks)";
        $sql = "SELECT t.* FROM ($white_list) w, tracks_selection t WHERE w.track_id=t.track_id AND w.track_id NOT IN($union)" . $this->getGenre("t.") . $this->getYear("t.");
        $userID = $this->system->getAuth()->getUser()->getID();
        $query = $this->system->getDB()->simpleQuery($sql, array(
            ":user" => $userID,
            ":tracklimit" => $this->trackLimit,
            ":artistlimit" => $this->artistLimit));
        if ($query->rowCount() != 0) {
            $data = null;
            $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            for ($i = 0; $i <= $num; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $this->printTrack($data, $data);
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
        $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
        $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
        $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
        $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
        $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
        $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks) UNION ($white_list)";
        $artists = "SELECT tm.artist_name FROM white_list w, tracks_masters tm WHERE w.user_id=:user AND w.track_id=tm.track_id GROUP BY tm.artist_name";
        $selected_tracks = "SELECT tm.track_id FROM ($artists) a, tracks_masters tm WHERE a.artist_name=tm.artist_name";
        $sql = "SELECT t.* FROM ($selected_tracks) s, tracks_selection t WHERE s.track_id=t.track_id AND s.track_id NOT IN($union)" . $this->getGenre("t.") . $this->getYear("t.");
        $userID = $this->system->getAuth()->getUser()->getID();
        $query = $this->system->getDB()->simpleQuery($sql, array(
            ":user" => $userID,
            ":tracklimit" => $this->trackLimit,
            ":artistlimit" => $this->artistLimit));
        if ($query->rowCount() != 0) {
            $data = null;
            $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            for ($i = 0; $i <= $num; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $this->printTrack($data);
        } else {
            $this->newOrUnknown(true);
        }
    }

    // Oblíbené album
    private function favouriteAlbum() {
        $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
        $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
        $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
        $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
        $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
        $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks) UNION ($white_list)";
        $albums = "SELECT t.release_id FROM track t, white_list w WHERE w.user_id=:user AND w.track_id=t.track_id GROUP BY t.release_id";
        $album_tracks = "SELECT t.track_id FROM ($albums) a, track t WHERE a.release_id=t.release_id";
        $sql = "SELECT t.* FROM ($album_tracks) at, tracks_selection t WHERE at.track_id=t.track_id AND at.track_id NOT IN($union)" . $this->getGenre("t.") . $this->getYear("t.");
        $userID = $this->system->getAuth()->getUser()->getID();
        $query = $this->system->getDB()->simpleQuery($sql, array(
            ":user" => $userID,
            ":tracklimit" => $this->trackLimit,
            ":artistlimit" => $this->artistLimit));
        if ($query->rowCount() != 0) {
            $data = null;
            $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            for ($i = 0; $i <= $num; $i++) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            $this->printTrack($data);
        } else {
            $this->newOrUnknown(true);
        }
    }

    // Doporuční novou nebo neznámou skladbu - populární
    private function recommendNewOrUnknownPopular() {
        // Zjištění zlomu popularity
        $data = $this->system->getDB()->query("SELECT popularity_break FROM popularity_break WHERE genre=? AND year=?", array($this->system->getURL()->getValue("genre"), $this->system->getURL()->getValue("year")));
        if (count($data) == 1) {
            $userID = $this->system->getAuth()->getUser()->getID();
            // Výběr kandidátů
            $wl = $this->system->getDB()->simpleQuery("SELECT t.track_id FROM white_list w, tracks_selection t WHERE w.user_id=? AND w.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t."), array($userID));
            if ($wl->rowCount() != 0) {
                $wlTrack = $this->randomSelection($wl);
                $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
                $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
                $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
                $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
                $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
                $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks) UNION ($white_list)";
                $selection = "SELECT track_id FROM tracks_selection WHERE popularity>=:pb" . $this->getYear() . $this->getGenre();
                $distance = "SELECT d.track_id2 AS track_id FROM white_list w, tracks_distance d WHERE w.user_id=:user AND w.track_id=d.track_id1";
                $sql = "SELECT s.track_id FROM (($distance) INTERSECT ($selection)) s WHERE s.track_id NOT IN($union)";
                $query = $this->system->getDB()->simpleQuery($sql, array(
                    ":user" => $userID,
                    ":tracklimit" => $this->trackLimit,
                    ":artistlimit" => $this->artistLimit,
                    ":pb" => $data[0]['popularity_break']
                ));
                if ($query->rowCount() != 0) {
                    $data = $this->randomSelection($query);
                    $data = $this->system->getDB()->simpleQuery("SELECT * FROM tracks_selection WHERE track_id=?", array($data['track_id']))->fetch();
                    $this->printTrack($data, $wlTrack);
                } else {
                    $this->newOrUnknownPopular();
                }
            } else {
                $this->newOrUnknownPopular();
            }
        } else {
            echo(json_encode(array('error' => 'No tracks for this request.')));
        }
    }

    // Doporuční novou nebo neznámou skladbu - nepopulární
    private function recommendNewOrUnknownUnpopular() {
        // Zjištění zlomu popularity
        $data = $this->system->getDB()->query("SELECT popularity_break FROM popularity_break WHERE genre=? AND year=?", array($this->system->getURL()->getValue("genre"), $this->system->getURL()->getValue("year")));
        if (count($data) == 1) {
            $userID = $this->system->getAuth()->getUser()->getID();
            // Výběr kandidátů
            $wl = $this->system->getDB()->simpleQuery("SELECT t.track_id FROM white_list w, tracks_selection t WHERE w.user_id=? AND w.track_id=t.track_id" . $this->getGenre("t.") . $this->getYear("t."), array($userID));
            if ($wl->rowCount() != 0) {
                $wlTrack = $this->randomSelection($wl);
                $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
                $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
                $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
                $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
                $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
                $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks) UNION ($white_list)";
                $selection = "SELECT track_id FROM tracks_selection WHERE popularity<:pb" . $this->getYear() . $this->getGenre();
                $distance = "SELECT d.track_id2 AS track_id FROM white_list w, tracks_distance d WHERE w.user_id=:user AND w.track_id=d.track_id1";
                $sql = "SELECT s.track_id FROM ($selection) s WHERE s.track_id IN($distance) AND s.track_id NOT IN($union)";
                $query = $this->system->getDB()->simpleQuery($sql, array(
                    ":user" => $userID,
                    ":tracklimit" => $this->trackLimit,
                    ":artistlimit" => $this->artistLimit,
                    ":pb" => $data[0]['popularity_break']
                ));
                if ($query->rowCount() != 0) {
                    $data = $this->randomSelection($query);
                    $data = $this->system->getDB()->simpleQuery("SELECT * FROM tracks_selection WHERE track_id=?", array($data['track_id']))->fetch();
                    $this->printTrack($data, $wlTrack);
                } else {
                    $this->newOrUnknownUnpopular();
                }
            } else {
                $this->newOrUnknownUnpopular();
            }
        } else {
            echo(json_encode(array('error' => 'No tracks for this request.')));
        }
    }

    // Nové nebo neznámé
    private function newOrUnknown($popular) {
        if ($this->system->getRandom()->bernoulliDistribution(0.8) == 1) {
            if ($popular) {
                $this->recommendNewOrUnknownPopular();
            } else {
                $this->recommendNewOrUnknownUnpopular();
            }
        } else {
            if ($popular) {
                $this->newOrUnknownPopular();
            } else {
                $this->newOrUnknownUnpopular();
            }
        }
    }

    // Nové/neznámé - populární
    private function newOrUnknownPopular() {
        // Zjištění zlomu popularity
        $data = $this->system->getDB()->query("SELECT popularity_break FROM popularity_break WHERE genre=? AND year=?", array($this->system->getURL()->getValue("genre"), $this->system->getURL()->getValue("year")));
        if (count($data) == 1) {
            // Výběr kandidátů
            $userID = $this->system->getAuth()->getUser()->getID();
            $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
            $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
            $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
            $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
            $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
            $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks) UNION ($white_list)";
            $selection = "SELECT track_id FROM tracks_selection WHERE popularity>=:pb" . $this->getGenre() . $this->getYear();
            $sql = "SELECT s.track_id FROM ($selection) s WHERE s.track_id NOT IN ($union)";
            $query = $this->system->getDB()->simpleQuery($sql, array(
                ":user" => $userID,
                ":tracklimit" => $this->trackLimit,
                ":artistlimit" => $this->artistLimit,
                ":pb" => $data[0]['popularity_break']
            ));
            if ($query->rowCount() != 0) {
                $data = $this->randomSelection($query);
                $data = $this->system->getDB()->simpleQuery("SELECT * FROM tracks_selection WHERE track_id=?", array($data['track_id']))->fetch();
                $this->printTrack($data);
            } else {
                echo(json_encode(array('error' => 'No tracks for this request.')));
            }
        } else {
            echo(json_encode(array('error' => 'No tracks for this request.')));
        }
    }

    // Nové/neznámé - nepopulární
    private function newOrUnknownUnpopular() {
        // Zjištění zlomu popularity
        $data = $this->system->getDB()->query("SELECT popularity_break FROM popularity_break WHERE genre=? AND year=?", array($this->system->getURL()->getValue("genre"), $this->system->getURL()->getValue("year")));
        if (count($data) == 1) {
            // Výběr kandidátů
            $userID = $this->system->getAuth()->getUser()->getID();
            $played_tracks = "SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :tracklimit";
            $played_artists = "SELECT tm.artist_name FROM (SELECT track_id FROM plays_history WHERE user_id=:user ORDER BY time DESC LIMIT :artistlimit) h, tracks_masters tm WHERE h.track_id=tm.track_id GROUP BY tm.artist_name";
            $played_artists_tracks = "SELECT t.track_id from ($played_artists) ma, tracks_masters t where t.artist_name=ma.artist_name";
            $black_list = "SELECT track_id FROM black_list WHERE user_id=:user";
            $white_list = "SELECT track_id FROM white_list WHERE user_id=:user";
            $union = "($black_list) UNION ($played_tracks) UNION ($played_artists_tracks) UNION ($white_list)";
            $selection = "SELECT track_id FROM tracks_selection WHERE popularity<:pb" . $this->getGenre() . $this->getYear();
            $sql = "SELECT s.track_id FROM ($selection) s WHERE s.track_id NOT IN ($union)";
            $query = $this->system->getDB()->simpleQuery($sql, array(
                ":user" => $userID,
                ":tracklimit" => $this->trackLimit,
                ":artistlimit" => $this->artistLimit,
                ":pb" => $data[0]['popularity_break']
            ));
            if ($query->rowCount() != 0) {
                $data = $this->randomSelection($query);
                $data = $this->system->getDB()->simpleQuery("SELECT * FROM tracks_selection WHERE track_id=?", array($data['track_id']))->fetch();
                $this->printTrack($data);
            } else {
                echo(json_encode(array('error' => 'No tracks for this request.')));
            }
        } else {
            echo(json_encode(array('error' => 'No tracks for this request.')));
        }
    }

    // Sestaví autory a název písničky
    private function findTitle($track) {
        $data = $this->system->getDB()->query("select m.* from tracks_selection t, masters_artists_joins m where t.track_id=? and t.master_id=m.master_id", array($track['track_id']));
        if (count($data) != 0) {
            $name = $data[0]['artist1'];
            foreach ($data as $item) {
                if ($item['artist1'] != "" & $item['artist2'] != "") {
                    $name .= " " . $item['join_relation'] . " " . $item['artist2'];
                }
            }
            return $name . " - " . $track['title'];
        } else {
            $data = $this->system->getDB()->query("select a.artist_name from track t, master m, masters_artists a where t.track_id=? and m.main_release=t.release_id and m.id=a.master_id", array($track['track_id']));
            $name = $data[0]['artist_name'];
            for ($k = 0; $k < 100; $k++) {
                $name = str_replace(" ($k)", "", $name);
            }
            return $name . " - " . $track['title'];
        }
    }

    // Najde Youtube video
    private function findYoutubeVideo($title) {
        $url = "https://www.googleapis.com/youtube/v3/search?q=" . urlencode($title) . "&maxResults=1&key=" . YOUTUBE_API_KEY . "&part=snippet";
        $result = file_get_contents($url);
        $result = json_decode($result, true);
        return $result['items'][0]['id']['videoId'];
    }

    // Vrať na výstup skladbu
    private function printTrack($data, $wl = null) {
        $this->playedTrack($data);
        $like = $this->system->getDB()->query("SELECT track_id FROM white_list WHERE user_id=? AND track_id=?", array($this->system->getAuth()->getUser()->getID(), $data['track_id']));
        $dislike = $this->system->getDB()->query("SELECT track_id FROM black_list WHERE user_id=? AND track_id=?", array($this->system->getAuth()->getUser()->getID(), $data['track_id']));
        $fullTitle = $this->findTitle($data);
        $json = array(
            'track_id' => $data['track_id'],
            'title' => $data['title'],
            'spotify_id' => $data['spotify_id'],
            'preview_url' => $data['preview_url'],
            'full_title' => $fullTitle . " (" . $data['year'] . ")",
            'youtube_code' => $this->findYoutubeVideo($fullTitle),
            'like' => (count($like) == 1) ? true : false,
            'dislike' => (count($dislike) == 1) ? true : false,
            'reccomended_tracks' => $this->recoomendTracks($wl)
        );
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
            if ($genreID != "0") {
                $genre = $this->system->getDB()->query("SELECT * FROM genre WHERE id=?", array($genreID));
                if (count($genre) != 0) {
                    return " AND " . $prefix . "genres LIKE ('%" . str_replace("'", "''", $genre[0]['name']) . "%')";
                } else {
                    return "";
                }
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

    // Provede náhodný výběr několika záznamů z dotazu
    private function randomSelectionOfMoreRecords($query, $number) {
        $data = null;
        $selected = array();
        // Vybere čísla náhodných záznamů
        $select = array();
        $end = ($number > $query->rowCount()) ? $query->rowCount() : $number;
        for ($i = 0; $i < $end; $i++) {
            do {
                $num = $this->system->getRandom()->uniformDistribution(0, $query->rowCount() - 1);
            } while (in_array($num, $select));
            $select[] = $num;
        }
        // Vybere záznamy
        for ($i = 0; $i <= $query->rowCount(); $i++) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if (in_array($i, $select)) {
                $selected[] = $data;
            }
        }
        return $selected;
    }

    // Vyhledat v databázi
    private function search() {
        if (isset($_POST['query'])) {
            $query = htmlspecialchars($_POST['query']);
            $data = array();
            $data['artists'] = $this->system->getDB()->simpleQuery("SELECT a.name, a.id FROM (SELECT artist_name AS name FROM tracks_masters WHERE LOWER(artist_name) LIKE LOWER(?) GROUP BY artist_name) tm, artist a WHERE a.name=tm.name", array($query . "%"))->fetchAll(PDO::FETCH_ASSOC);
            $data['masters'] = $this->system->getDB()->simpleQuery("SELECT m.main_release AS id, m.title, string_agg(a.artist_name, ', ') AS artist FROM master m, masters_artists a WHERE m.id IN(SELECT master_id AS id FROM tracks_selection) AND LOWER(m.title) LIKE LOWER(?) AND m.id=a.master_id GROUP BY m.id, m.title", array($query . "%"))->fetchAll(PDO::FETCH_ASSOC);
            $data['tracks'] = $this->system->getDB()->simpleQuery("SELECT t.track_id, t.title, string_agg(m.artist_name, ', ') AS artist FROM tracks_selection t, masters_artists m WHERE LOWER(t.title) LIKE LOWER(?) AND t.spotify_id IS NOT NULL AND t.master_id=m.master_id GROUP BY t.track_id, t.title", array($query . "%"))->fetchAll(PDO::FETCH_ASSOC);
            $likes = $this->system->getDB()->query("SELECT track_id FROM white_list WHERE user_id=?", array($this->system->getAuth()->getUser()->getID()));
            $l = array();
            foreach ($likes as $like) {
                $l[] = $like['track_id'];
            }
            for ($i = 0; $i < count($data['tracks']); $i++) {
                if (in_array($data['tracks'][$i]['track_id'], $l)) {
                    $data['tracks'][$i]['like'] = true;
                } else {
                    $data['tracks'][$i]['like'] = false;
                }
            }
            echo(json_encode($data));
        }
    }

    // Získá umělce podle ID
    private function getArtist() {
        if (isset($_POST['artist_id'])) {
            $id = intval($_POST['artist_id']);
            $query = $this->system->getDB()->simpleQuery("SELECT * FROM artist WHERE id=?", array($id));
            if ($query->rowCount() != 0) {
                // Profil interpreta
                $data = $query->fetch(PDO::FETCH_ASSOC);
                // Alba interpreta
                $data['masters'] = $this->system->getDB()->query("SELECT m.main_release AS id, m.title, m.year, m.main_release FROM master m, masters_artists a WHERE m.id=a.master_id AND a.artist_name=? AND m.id IN(SELECT master_id FROM tracks_selection) ORDER BY m.year DESC", array($data['name']));
                echo(json_encode($data));
            } else {
                echo(json_encode(array('error' => 'This artist does not exist.')));
            }
        } else {
            echo(json_encode(array('error' => 'Wrong parameter.')));
        }
    }

    // Získá album podle ID
    private function getAlbum() {
        if (isset($_POST['release_id'])) {
            $id = intval($_POST['release_id']);
            $query = $this->system->getDB()->simpleQuery("SELECT * FROM release WHERE id=?", array($id));
            if ($query->rowCount() != 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
                $data['tracks'] = $this->system->getDB()->query("SELECT * FROM track WHERE release_id=? ORDER BY trackno, position", array($id));
                $likes = $this->system->getDB()->query("SELECT track_id FROM white_list WHERE user_id=?", array($this->system->getAuth()->getUser()->getID()));
                $l = array();
                foreach ($likes as $like) {
                    $l[] = $like['track_id'];
                }
                for ($i = 0; $i < count($data['tracks']); $i++) {
                    if (in_array($data['tracks'][$i]['track_id'], $l)) {
                        $data['tracks'][$i]['like'] = true;
                    } else {
                        $data['tracks'][$i]['like'] = false;
                    }
                }
                echo(json_encode($data));
            } else {
                echo(json_encode(array('error' => 'This album does not exist.')));
            }
        } else {
            echo(json_encode(array('error' => 'Wrong parameter.')));
        }
    }

    // Získá seznam oblíbených skladeb
    private function getWhiteList() {
        $tracks = $this->system->getDB()->simpleQuery("SELECT t.track_id, t.title, string_agg(m.artist_name, ', ') AS artist FROM white_list w, tracks_selection t, masters_artists m WHERE w.track_id=t.track_id AND w.user_id=? AND t.master_id=m.master_id GROUP BY t.track_id, t.title, w.id ORDER BY w.id DESC", array($this->system->getAuth()->getUser()->getID()))->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($tracks));
    }

    // Získá seznam neoblíbených skladeb
    private function getBlackList() {
        $tracks = $this->system->getDB()->simpleQuery("SELECT t.track_id, t.title, string_agg(m.artist_name, ', ') AS artist FROM black_list b, tracks_selection t, masters_artists m WHERE b.track_id=t.track_id AND b.user_id=? AND t.master_id=m.master_id GROUP BY t.track_id, t.title, b.id ORDER BY b.id DESC", array($this->system->getAuth()->getUser()->getID()))->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($tracks));
    }

    // Získá seznam žánrů
    private function getGenres() {
        $data = $this->system->getDB()->simpleQuery("SELECT id, name FROM genre ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($data));
    }

    // Získá seznam desetiletí
    private function getYears() {
        $data = $this->system->getDB()->simpleQuery("SELECT id, name FROM year ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        echo(json_encode($data));
    }

    // Výpočet indexu popularity oblíbených interpretů
    private function indexOfPopularity() {
        $sql = "SELECT SUM(a.popularity * ua.count) / SUM(ua.count) AS index FROM " .
                "(SELECT m.artist_name, COUNT(m.artist_name) AS count FROM white_list w, tracks_selection t, masters_artists m WHERE user_id=? AND w.track_id=t.track_id AND t.master_id=m.master_id GROUP BY m.artist_name) ua, artist a " .
                "WHERE ua.artist_name=a.name";
        return $this->system->getDB()->query($sql, array($this->system->getAuth()->getUser()->getID()))[0]['index'];
    }

    // Získá skladbu
    private function getTrack() {
        if ($this->system->getURL()->getArg(1) != "") {
            $data = $this->system->getDB()->query("SELECT * FROM tracks_selection WHERE track_id=?", array($this->system->getURL()->getArg(1)));
            if (count($data) != 0) {
                $this->printTrack($data[0]);
            } else {
                echo(json_encode(array('error' => 'This track does not exist.')));
            }
        } else {
            echo(json_encode(array('error' => 'Wrong argument.')));
        }
    }

    // Doporučí 5 doplňujících písniček
    private function recoomendTracks($wlTrack = null) {
        if ($wlTrack == null) {
            $sql = "SELECT w.track_id FROM white_list w WHERE w.user_id=?";
            $query = $this->system->getDB()->simpleQuery($sql, array($this->system->getAuth()->getUser()->getID()));
            if ($query->rowCount() == 0) {
                return array();
            } else {
                $wlTrack = $this->randomSelection($query);
            }
        }
        // Vyber blížké písničky
        $sql = "SELECT track_id2 FROM tracks_distance WHERE track_id1=?";
        $query = $this->system->getDB()->simpleQuery($sql, array($wlTrack['track_id']));
        $selected = $this->randomSelectionOfMoreRecords($query, 4);
        // Vytvoření dotazu
        $sql = "SELECT t.track_id, t.title, string_agg(m.artist_name, ', ') AS artist FROM tracks_selection t, masters_artists m";
        $args = array();
        $sql .= " WHERE (t.track_id=?";
        $args[] = $wlTrack['track_id'];
        for ($i = 0; $i < count($selected); $i++) {
            $sql .= " OR t.track_id=?";
            $args[] = $selected[$i]['track_id2'];
        }
        $sql .= ") AND t.master_id=m.master_id GROUP BY t.track_id, title";
        $data = $this->system->getDB()->query($sql, $args);
        $result = array();
        // Uspořádání
        $add = array();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['track_id'] == $wlTrack['track_id']) {
                $result['like'] = $data[$i];
            } else {
                $add[] = $data[$i];
            }
        }
        // Zjištění oblíbených a neoblíbených
        $likes = $this->system->getDB()->query("SELECT track_id FROM white_list WHERE user_id=?", array($this->system->getAuth()->getUser()->getID()));
        $dislike = $this->system->getDB()->query("SELECT track_id FROM black_list WHERE user_id=?", array($this->system->getAuth()->getUser()->getID()));
        $l1 = array();
        $l2 = array();
        foreach ($likes as $like) {
            $l1[] = $like['track_id'];
        }
        foreach ($dislikes as $dislike) {
            $l2[] = $dislike['track_id'];
        }
        for ($i = 0; $i < count($add); $i++) {
            // Like
            if (in_array($add[$i]['track_id'], $l1)) {
                $add[$i]['like'] = true;
            } else {
                $add[$i]['like'] = false;
            }
            // Dislike
            if (in_array($add[$i]['track_id'], $l2)) {
                $add[$i]['dislike'] = true;
            } else {
                $add[$i]['dislike'] = false;
            }
        }
        $result['may_like'] = $add;
        return $result;
    }

}
