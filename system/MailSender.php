<?php

require_once '../config.php';

class MailSender {

    private $from;
    private $to;
    private $subject;
    private $message;

    public function __construct() {
        $this->from = WEB_EMAIL;
        $this->clear();
    }

    // Vyčisti informace
    public function clear() {
        $this->to = "";
        $this->subject = "";
        $this->message = "";
    }

    // Odešli email
    public function send() {
        $headers = 'From: ' . $this->from . "\r\n" .
                'Reply-To: ' . $this->from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        if (mb_send_mail($this->to, $this->subject, $this->message, $headers)){
            $this->clear();
            return true;
        } else {
            $this->clear();
            return false;
        }
    }
    
    // Odeslat registrační email
    public function sendRegistrationMail($to, $id, $activeKey){
        $this->to = $to;
        $this->subject = "PlayMC: Aktivace účtu";
        $this->message = "Dobrý den,\n"
                . "přijali jsme Vaši žádost o registraci na " . WEB_NAME . ". Pro aktivaci účtu klikněte na následující odkaz:\n"
                . WEB_URL . "index.php?registration&active&" . $id . "&" . $activeKey . "\n"
                . "\n"
                . "S pozdravem,\n"
                . WEB_NAME;
        $this->send();
    }
}
