<?php

// load Smarty library
require_once('../lib/smarty/libs/Smarty.class.php');
require_once ('../config.php');

class SmartyMCRec extends Smarty {

    function __construct() {
        parent::__construct();

        $this->setTemplateDir('../views/templates/');
        $this->setCompileDir('../views/templates_c/');
        $this->setConfigDir('../views/configs/');
        $this->setCacheDir('../views/cache/');

        $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $this->assign('appName', WEB_NAME);
    }
    
    public function setWebPath($url){
        $this->assign('webPath', $url);
    }
}
