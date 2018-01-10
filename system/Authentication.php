<?php

require_once '../system/User.php';

class Authentication {

    private $user = null;

    public function __construct() {
        
    }

    // Je uživatel přihlášen?
    public function isLogin() {
        if (isset($_SESSION['auth_user_id']) && $_SESSION['auth_user_id'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Přihlásí uživatele
    public function login($data) {
        $_SESSION['auth_user_id'] = $data['id'];
        $this->loadUser($data);
    }
    
    // Odhlásí uživatele
    public function logout(){
        unset($_SESSION['auth_user_id']);
    }

    // Načti uživatele
    public function loadUser($data) {
        $this->user = new User($data['id'], $data['name'], $data['surname'], $data['email']);
    }

    // Získá uživatele
    public function getUser() {
        return $this->user;
    }

}
