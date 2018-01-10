<?php

require_once '../config.php';

class URLParser {

    private $url;
    private $args = array();

    public function __construct() {
        $this->url = WEB_URL;
        foreach ($_GET as $item => $data) {
            $this->args[] = $item;
        }
        //(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    // Získat adresu
    public function getPath() {
        return $this->url;
    }

    // Získat argument z URL
    public function getArg($index) {
        return (isset($this->args[$index])) ? $this->args[$index] : "";
    }

    // Přesměrovat na adresu
    public function redirect($destination = array()) {
        $args = "";
        for ($i = 0; $i < count($destination); $i++) {
            if ($i = 0) {
                $args .= "?";
            }
            $args .= $destination[$i];
            if ($i != count($destination) - 1) {
                $args .= "&";
            }
        }
        header('Location: ' . $this->url . "index.php" . $args);
        exit();
    }
    
    public function getValue($attribute){
        return (isset($_GET[$attribute])) ? $_GET[$attribute] : '';
    }
}
