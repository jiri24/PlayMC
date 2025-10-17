<?php

require_once '../system/SmartyPlayMC.php';
require_once '../system/URLParser.php';
require_once '../system/Database.php';
require_once '../system/MailSender.php';
require_once '../system/Authentication.php';
require_once '../system/Random.php';

class System {

    private $template;
    private $url;
    private $database;
    private $mail;
    private $authentication;
    private $random;

    public function __construct() {
        $this->template = new SmartyPlayMC();
        $this->template->caching = 0;
        $this->url = new URLParser();
        $this->database = new Database();
        $this->authentication = new Authentication();
        $this->mail = new MailSender();
        $this->template->setWebPath($this->url->getPath());
        $this->random = new Random();
        // Načte uživatele, pokud je přihlášen
        if ($this->authentication->isLogin()) {
            $data = $this->database->query("SELECT * FROM users WHERE id=?", array($_SESSION['auth_user_id']));
            $this->authentication->loadUser($data[0]);
        }
    }

    // Získání správce šablony
    public function getTemplate() {
        return $this->template;
    }

    // Získání URL Parseru
    public function getURL() {
        return $this->url;
    }

    // Získání databáze
    public function getDB() {
        return $this->database;
    }

    // Získá emailový klient
    public function getMail() {
        return $this->mail;
    }

    // Získat authenticator
    public function getAuth() {
        return $this->authentication;
    }

    // Získá generátor náhodných čísel
    public function getRandom() {
        return $this->random;
    }

    // Získá a zkontroluje obsah POST dotazu
    public function getResponse() {
        $response = array();
        foreach ($_POST as $item => $data) {
            $response[$item] = htmlspecialchars($data);
        }
        return $response;
    }

    // Vygeneruje náhodný řetězec
    public function generateString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // Zahashuje heslo
    public function hashPassword($password, $salt) {
        return password_hash($password . $salt, PASSWORD_BCRYPT);
    }

    // Nastavení přihlášení
    public function checkLogin() {
        if ($this->authentication->isLogin()) {
            $this->template->setLogin(1);
            $this->template->setUser($this->authentication->getUser());
        } else {
            $this->template->setLogin(0);
        }
    }

}
