<?php

namespace Example\Core;

use PDO;
use PDOException;

class Database
{
    private $database;

    public function get()
    {
        if (!$this->database) {
            try {
                $options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING];
                $this->database = new PDO(
                   Config::get('DB_TYPE') . ':host=' . Config::get('DB_HOST') . ';dbname=' .
                   Config::get('DB_NAME') . ';port=' . Config::get('DB_PORT') . ';charset=' . Config::get('DB_CHARSET'),
                   Config::get('DB_USER'), Config::get('DB_PASS'), $options
                );
            } catch (PDOException $e) {
                echo $e;
            }
        }
        return $this->database;
    }
}
