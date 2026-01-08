<?php
namespace Config;

use PDO;
use PDOException;
class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "hospitalmanagement";
    public $conn;

    public function connect()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conn;

        } catch (PDOException $e) {
            die("Connection failed");
        }
    }
}
