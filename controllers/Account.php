<?php

class Account {

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
            case "settings":
                $this->settings();
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
                // Je vše vyplněné?
                if (isset($response['auth_email']) && $response['auth_email'] != '' &&
                        isset($response['auth_password']) && $response['auth_email'] != '') {
                    $data = $this->system->getDB()->query("SELECT * FROM users WHERE email=? AND password=?", array($response['auth_email'], $this->system->hashPassword($response['auth_password'], $response['auth_email'])));
                    if (count($data) == 1) {
                        $this->system->getAuth()->login($data[0]);
                        $this->system->getURL()->redirect('');
                    } else {
                        $errors = array();
                        $errors[] = "Chybný e-mail nebo heslo.";
                        $this->system->getTemplate()->assign("messageDanger", $errors);
                        $this->system->getTemplate()->setOutput('login.tpl');
                    }
                } else {
                    $errors = array();
                    $errors[] = "Všechna pole jsou povinná.";
                    $this->system->getTemplate()->assign("messageDanger", $errors);
                    $this->system->getTemplate()->setOutput('login.tpl');
                }
            } else {
                $this->system->getTemplate()->setOutput('login.tpl');
            }
        }
    }

    // Odhlášení
    private function logout() {
        $this->system->getAuth()->logout();
        $this->system->getURL()->redirect();
    }

    // Nastavení
    private function settings() {
        if ($this->system->getAuth()->isLogin()) {
            $user = $this->system->getAuth()->getUser();
            $this->system->getTemplate()->assign("setName", $user->getName());
            $this->system->getTemplate()->assign("setSurname", $user->getSurname());
            $this->system->getTemplate()->assign("setEmail", $user->getEmail());

            $response = $this->system->getResponse();
            if (count($response) > 0) {
                // Jsou vyplněna povinná pole?
                if (isset($response['set_name']) && $response['set_name'] != '' &&
                        isset($response['set_surname']) && $response['set_surname'] != '' &&
                        isset($response['set_email']) && $response['set_email'] != '') {
                    // Aktualizuj nastavení
                    $data = array();
                    $data[] = $response['set_name'];
                    $data[] = $response['set_surname'];
                    $data[] = $response['set_email'];
                    if ((isset($response['set_password_old']) && $response['set_password_old'] != "") || (isset($response['set_password_new']) && $response['set_password_new'] != "") || (isset($response['set_password_confirm']) && $response['set_password_confirm'] != "")) {
                        // Změň heslo
                        $errors = array();
                        $passwordError = false;
                        if (!isset($response['set_password_old']) || $response['set_password_old'] == "") {
                            $errors[] = "Nebylo zadáno staré heslo.";
                            $passwordError = true;
                        }
                        if (!isset($response['set_password_new']) || $response['set_password_new'] == "") {
                            $errors[] = "Nebylo zadáno nové heslo.";
                            $passwordError = true;
                        }
                        if (!isset($response['set_password_confirm']) || $response['set_password_confirm'] == "") {
                            $errors[] = "Nebylo zadáno staré heslo.";
                            $passwordError = true;
                        }
                        $actualPassword = $this->system->getDB()->query("SELECT password FROM users WHERE id=?", array($user->getID()));
                        if ($actualPassword[0]['password'] != $this->system->hashPassword($response['set_password_old'], $user->getEmail())) {
                            $errors[] = "Zadané heslo není správné.";
                            $passwordError = true;
                        }
                        if (!$passwordError) {
                            if (!$this->checkPassword($response['set_password_new'], $response['set_password_confirm'])) {
                                $errors[] = "Nepodařilo se změnit heslo.";
                                $passwordError = true;
                            }
                        }
                        if ($passwordError) {
                            $this->system->getTemplate()->assign('messageDanger', $errors);
                        } else {
                            $data[] = $this->system->hashPassword($response['set_password_new'], $response['set_email']);
                            $data[] = $user->getID();
                            $this->system->getDB()->update("UPDATE users SET name=?, surname=?, email=?, password=? WHERE id=?", $data);
                            $this->system->getTemplate()->assign('messageSuccess', array("Nastavení bylo úspěšně uloženo."));
                        }
                    } else {
                        // Heslo se nemění
                        $data[] = $user->getID();
                        $this->system->getDB()->update("UPDATE users SET name=?, surname=?, email=? WHERE id=?", $data);
                        $this->system->getTemplate()->assign('messageSuccess', array("Nastavení bylo úspěšně uloženo."));
                    }
                    // Vlož do formuláře aktualizované údaje
                    $this->system->getTemplate()->assign("setName", $data[0]);
                    $this->system->getTemplate()->assign("setSurname", $data[1]);
                    $this->system->getTemplate()->assign("setEmail", $data[2]);
                } else {
                    $this->system->getTemplate()->assign('messageDanger', array("Pole jméno, příjmení a email jsou povinná."));
                }
            }

            $this->system->getTemplate()->setOutput('settings.tpl');
        } else {
            $this->system->getTemplate()->error("Nejste přihášený.");
        }
    }

    // Zkontroluje heslo
    private function checkPassword($new, $confirm) {
        if (mb_strlen($new) < 6) {
            return false;
        } elseif ($new != $confirm) {
            return false;
        } else {
            return true;
        }
    }

}
