<?php

require_once '../config.php';

class Database {

    private $connection;

    public function __construct() {
        try {
            $this->connection = new PDO("pgsql:dbname=" . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Nepodařilo se připojit k databázi: " . $e->getMessage());
        }
    }

    // Dotaz
    public function query($sql, $values = array()) {
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($values);
            return $query->fetchAll();
        } catch (PDOException $e) {
            die("Chyba dotazu na databázi: " . $e->getMessage());
        }
    }
    
    // Dotaz, který nevrátí ihned výsledek
    public function simpleQuery($sql, $values = array()) {
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($values);
            return $query;
        } catch (PDOException $e) {
            die("Chyba dotazu na databázi: " . $e->getMessage());
        }
    }

    // Vložení
    public function insert($sql, $values) {
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($values);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            die("Chyba vložení do databáze: " . $e->getMessage());
        }
    }

    // Aktualizace
    public function update($sql, $values) {
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($values);
        } catch (PDOException $e) {
            die("Chyba při aktualizaci databáze: " . $e->getMessage());
        }
    }

}
