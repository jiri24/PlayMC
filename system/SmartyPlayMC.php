<?php

// load Smarty library
require_once('../lib/smarty/libs/Smarty.class.php');
require_once('../system/User.php');
require_once ('../config.php');

class SmartyPlayMC extends Smarty {
    private $output = "";

    function __construct() {
        parent::__construct();

        $this->setTemplateDir('../views/templates/');
        $this->setCompileDir('../views/templates_c/');
        $this->setConfigDir('../views/configs/');
        $this->setCacheDir('../views/cache/');

        $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $this->assign('appName', WEB_NAME);
        $this->assign('messageDanger', array());
        $this->assign('messageSuccess', array());
        $this->cashing = false;
    }
    
    // Nastavení výstupu
    public function setOutput($value){
        $this->output = $value;
    }
    
    // Vytisknutí obsahu
    public function printOutput(){
        if ($this->output==""){
            $this->output = "index.tpl";
        }
        $this->display($this->output);
    }
    
    // Chybová stránka
    public function error($message = ""){
        if ($message == ""){
            $message = "Požadovaná stránka nebyla nalezena.";
        }
        $this->assign("messageDanger", array($message));
        $this->output = "error.tpl";
    }
    
    // Stránka s pozitivní odezvou
    public function success($header = "", $message = ""){
        if ($header == ""){
            $header = "Úspěšně dokončeno";
        }
        if ($message == ""){
            $message = "Vše proběhlo správně.";
        }
        $this->assign("messageSuccess", array($message));
        $this->assign("messageHeader", $header);
        $this->output = "success.tpl";
    }
    
    // Nastavení webové adresy
    public function setWebPath($url){
        $this->assign('webPath', $url);
    }
    
    // Nastaví proměnnou identifikující přihlášení
    public function setLogin($bool){
        $this->assign('userLogin', $bool);
    }
    
    // Nastavení informací o uživateli
    public function setUser($user){
        $this->assign('userFullName', $user->getFullName());
    }
}
