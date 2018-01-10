<?php

class Users {

    private $system;

    public function __construct(System $system) {
        $this->system = $system;
        switch ($this->system->getURL()->getArg(1)) {
            case "login":
                $this->login();
                break;
            case "logout":
                $this->logout();
                break;
            default :
                $system->getTemplate()->error();
                break;
        }
    }

    // Přihlášení
    private function login() {
        if ($this->system->getAuth()->isLogin()) {
            $this->system->getTemplate()->error("Už jste přihlášen.");
        } else {
            $response = $this->system->getResponse();
            if (count($response) > 0) {
                $data = $this->system->getDB()->query("SELECT * FROM users WHERE email=? AND password=?", array($response['auth_email'], $this->system->hashPassword($response['auth_password'], $response['auth_email'])));
                if (count($data) == 1) {
                    $this->system->getAuth()->login($data[0]);
                    $this->system->getTemplate()->success("Úspěšně přihlášen", "Byl jste úspěšně přihlášen ke svému účtu.");
                } else {
                    $errors = array();
                    $errors[] = "Chybný e-mail nebo heslo.";
                    $this->system->getTemplate()->assign("messageDanger", $errors);
                    $this->system->getTemplate()->setOutput('login.tpl');
                }
            } else {
                $this->system->getTemplate()->setOutput('login.tpl');
            }
        }
    }
    
    // Odhlášení
    private function logout(){
        $this->system->getAuth()->logout();
        $this->system->getURL()->redirect();
    }

}
