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
            case "forgot-password":
                $this->forgotPassword();
                break;
            case "reset-password":
                $this->resetPassword();
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
                    $data = $this->system->getDB()->query("SELECT * FROM users WHERE email=?", array($response['auth_email']));
                    if (count($data) == 1) {
                        if (password_verify($response['auth_password'] . "PlayMC", $data[0]['password'])) {
                            $this->system->getAuth()->login($data[0]);
                            $this->system->getURL()->redirect('');
                        } else {
                            $errors = array();
                            $errors[] = "Chybné heslo.";
                            $this->system->getTemplate()->assign("messageDanger", $errors);
                            $this->system->getTemplate()->setOutput('login.tpl');
                        }
                    } else {
                        $errors = array();
                        $errors[] = "Chybný e-mail.";
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
                        if (!password_verify($response['set_password_old'] . "PlayMC", $actualPassword[0]['password'])) {
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
                            $data[] = $this->system->hashPassword($response['set_password_new'], "PlayMC");
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

    // Zapomenuté heslo
    private function forgotPassword() {
        $response = $this->system->getResponse();
        if (count($response) != 0) {
            if (isset($response['email']) && $response['email'] != "") {
                $data = $this->system->getDB()->query("SELECT * FROM users WHERE email=?", array($response['email']));
                if (count($data) == 1) {
                    // Zkontroluj čas
                    $time = true;
                    if ($data[0]['pwd_reset_time'] != null) {
                        if (strtotime($data[0]['pwd_reset_time']) + 24 * 60 * 60 > time()) {
                            $time = false;
                        } else {
                            $time = true;
                        }
                    }
                    if ($time) {
                        // Odešle email
                        $key = $this->system->generateString(8);
                        $this->system->getMail()->sendResetPasswordMail($response['email'], $data[0]['id'], $key);
                        // Uloží kód a čas do databáze
                        $this->system->getDB()->update("UPDATE users SET pwd_reset_key=?, pwd_reset_time=? WHERE id=?", array($key, date('Y-m-d H:i:s', time()), $data[0]['id']));
                        $this->system->getTemplate()->assign('messageSuccess', array("Odkaz pro resetování hesla byl úspěšně odeslán na vaši emailovou adresu."));
                    } else {
                        $this->system->getTemplate()->assign('messageDanger', array('Vaše heslo nelze resetovat. Zkuste to prosím později.'));
                    }
                } else {
                    $this->system->getTemplate()->assign('messageDanger', array('Uživatel se zadanou emailovou adresou nebyl nalezen.'));
                }
            } else {
                $this->system->getTemplate()->assign('messageDanger', array('Formulář byl chybně vyplněn.'));
            }
        }
        $this->system->getTemplate()->setOutput('forgot_password.tpl');
    }

    // Obnovení hesla
    private function resetPassword() {
        $id = intval($this->system->getURL()->getArg(2));
        $key = $this->system->getURL()->getArg(3);
        if ($id != 0 && $key != "") {
            $data = $this->system->getDB()->query("SELECT * FROM users WHERE id=? AND pwd_reset_key=?", array($id, $key));
            if (count($data) == 1) {
                $response = $this->system->getResponse();
                if (count($response) > 0) {
                    if ((isset($response['set_password_new']) && $response['set_password_new'] != "") || (isset($response['set_password_confirm']) && $response['set_password_confirm'] != "")) {
                        // Změň heslo
                        $errors = array();
                        $passwordError = false;
                        if (!isset($response['set_password_new']) || $response['set_password_new'] == "") {
                            $errors[] = "Nebylo zadáno nové heslo.";
                            $passwordError = true;
                        }
                        if (!isset($response['set_password_confirm']) || $response['set_password_confirm'] == "") {
                            $errors[] = "Nebylo zadáno staré heslo.";
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
                            $save = array();
                            $save[] = $this->system->hashPassword($response['set_password_new'], "PlayMC");
                            $save[] = $data[0]['id'];
                            $this->system->getDB()->update("UPDATE users SET password=? WHERE id=?", $save);
                            $this->system->getTemplate()->assign('messageSuccess', array("Heslo bylo úspěšně obnoveno, můžete se přihlásit."));
                        }
                    }
                }
                $this->system->getTemplate()->setOutput('reset_password.tpl');
            } else {
                $this->system->getTemplate()->error("Neplatný požadavek.");
            }
        } else {
            $this->system->getTemplate()->error("Neplatný požadavek.");
        }
    }

}
