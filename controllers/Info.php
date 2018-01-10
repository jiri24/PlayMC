<?php

class Info {

    private $system;

    public function __construct(System $system) {
        $this->system = $system;
        
        if ($this->system->getAuth()->isLogin()){
            $this->system->getTemplate()->assign('genres', $this->system->getDB()->query("SELECT * FROM genre"));
            $this->system->getTemplate()->setOutput("home.tpl");
        } else {
            $this->system->getTemplate()->setOutput("default.tpl");
        }
    }

}
