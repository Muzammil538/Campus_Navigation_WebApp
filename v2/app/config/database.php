<?php
class Database {
    private $host = "localhost";
    private $db_name = "campus_tailwind";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        if ($this->conn) return $this->conn;

        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("DB connection error");
        }
        return $this->conn;
    }
}
