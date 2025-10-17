<?php

class Registration {

    private $system;

    public function __construct(System $system) {
        $this->system = $system;
        switch ($this->system->getURL()->getArg(1)) {
            case "register":
                $this->register();
                break;
            case "active":
                $this->active();
                break;
            default :
                $system->getTemplate()->error();
                break;
        }
    }

    private function register() {
        $response = $this->system->getResponse();
        $regName = "";
        $regSurname = "";
        $regEmail = "";
        if (count($response) > 0) {
            // Byla odeslána data, zkontroluj je
            $captcha = $this->checkCaptcha($response["g-recaptcha-response"]);
            $password = $this->checkPassword($response);
            $email = $this->checkEmail($response['reg_email']);
            $required = $this->checkRequired($response);
            if ($captcha && $password && $email) {
                $this->registerUser($response);
                $this->system->getTemplate()->setOutput('register_success.tpl');
            } else {
                $errors = array();
                if (!$captcha)
                    $errors[] = "Antispanová kontrola nebyla úspěšná.";
                if (!$password)
                    $errors[] = "Heslo nemá platný formát.";
                if (!$email)
                    $errors[] = "Uživatel s touto e-mailovou adresou již existuje.";
                if (!$required)
                    $errors[] = "Všechna pole jsou povinná.";
                $this->system->getTemplate()->assign('messageDanger', $errors);
                $regName = $response['reg_name'];
                $regSurname = $response['reg_surname'];
                $regEmail = $response['reg_email'];
                $this->system->getTemplate()->setOutput('register.tpl');
            }
        } else {
            // Nic nebylo odesláno, vytiskni formulář
            $this->system->getTemplate()->setOutput('register.tpl');
        }
        // Vyplň formulář
        $this->system->getTemplate()->assign('regName', $regName);
        $this->system->getTemplate()->assign('regSurname', $regSurname);
        $this->system->getTemplate()->assign('regEmail', $regEmail);
    }

    // Zkontroluj captchu
    private function checkCaptcha($code) {
        require_once '../config.php';
        $response = $code;
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $_POST["g-recaptcha-response"]
        );
        $query = http_build_query($data);
        $options = array(
            'http' => array(
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Content-Length: " . strlen($query) . "\r\n" .
                "User-Agent:PlayMC/1.0\r\n",
                'method' => "POST",
                'content' => $query,
            ),
        );
        $context = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success = json_decode($verify);
        if ($captcha_success->success == true) {
            return true;
        } else {
            return false;
        }
    }

    // Kontrola hesla
    private function checkPassword($response) {
        if ($response['reg_password'] != $response['reg_password_confirm']) {
            return false;
        } elseif (mb_strlen($response['reg_password']) < 6) {
            return false;
        } else {
            return true;
        }
    }
    
    // Zkontroluje povinná pole
    private function checkRequired($response){
        if (!isset($response['reg_name']) || $response['reg_name'] == '' ||
                !isset($response['reg_surname']) || $response['reg_surname'] == '' ||
                !isset($response['reg_email']) || $response['reg_email'] == '' ||
                !isset($response['reg_password']) || $response['reg_password'] == '' ||
                !isset($response['reg_password_confirm']) || $response['reg_password_confirm'] == ''){
            return false;
        } else {
            return true;
        }
    }
    
    // Zkontroluje email
    private function checkEmail($email){
        $data = $this->system->getDB()->query("SELECT * FROM users WHERE email=?", array($email));
        if (count($data) == 0){
            return true;
        } else {
            return false;
        }
    }

    // Provede registraci uživatele
    private function registerUser($values) {
        $data = array(
            'name' => $values['reg_name'],
            'surname' => $values['reg_surname'],
            'email' => $values['reg_email'],
            'password' => $this->system->hashPassword($values['reg_password'], "PlayMC"),
            'active_key' => $this->system->generateString(6),
            'active' => 0);
        $this->system->getDB()->insert("INSERT INTO users (name, surname, email, password, active_key, active) VALUES(:name,:surname,:email,:password,:active_key,:active)", $data);
        $id = $this->system->getDB()->query("SELECT id FROM users WHERE email=?", array($data['email']))[0]['id'];
        $this->system->getMail()->sendRegistrationMail($data['email'], $id, $data['active_key']);
    }
    
    // Aktivace účtu
    private function active(){
        $id = $this->system->getURL()->getArg(2);
        $key = $this->system->getURL()->getArg(3);
        if ($id != "" && $key != ""){
            $data = $this->system->getDB()->query("SELECT active FROM users WHERE id=? AND active_key=?", array($id, $key));
            if (count($data) == 1){
                if ($data[0]['active'] == 0){
                    $this->system->getDB()->update("UPDATE users SET active=? WHERE id=? AND active_key=?", array(1, $id, $key));
                    $this->system->getTemplate()->setOutput("active_success.tpl");
                } else {
                    $this->system->getTemplate()->error("Účet už je aktivní.");
                }
            } else {
                $this->system->getTemplate()->error("Chybný uživatel nebo aktivační kód.");
            }
        } else {
            $this->system->getTemplate()->error();
        }
    }
}
