<?php

class User {

    private $id;
    private $name;
    private $surname;
    private $email;

    public function __construct($id, $name, $surname, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
    }

    // Získá jméno a příjmení
    public function getFullName(){
        return $this->name . " " . $this->surname;
    }
    
    // Získá jméno
    public function getName(){
        return $this->name;
    }
    
    // Získá příjmení
    public function getSurname(){
        return $this->surname;
    }
    
    // Získá e-mail
    public function getEmail(){
        return $this->email;
    }
    
    // Získá ID
    public function getID(){
        return $this->id;
    }
}
