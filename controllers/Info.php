<?php

class Info {

    private $system;

    public function __construct(System $system) {
        $this->system = $system;

        switch ($this->system->getURL()->getArg(1)) {
            case "help":
                if ($this->system->getAuth()->isLogin()) {
                    $this->system->getTemplate()->setOutput("help.tpl");
                } else {
                    $this->system->getTemplate()->setOutput("default.tpl");
                }
                break;
            default:
                if ($this->system->getAuth()->isLogin()) {
                    $this->system->getTemplate()->setOutput("home.tpl");
                } else {
                    $this->system->getTemplate()->setOutput("default.tpl");
                }
        }
    }

}
