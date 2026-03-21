<?php
class Database {
    private $host = "localhost";
    private $db_name = "campus_navigator";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $initCommandAttr = defined('Pdo\Mysql::ATTR_INIT_COMMAND')
                ? constant('Pdo\Mysql::ATTR_INIT_COMMAND')
                : PDO::MYSQL_ATTR_INIT_COMMAND;
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    $initCommandAttr => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            die("Database connection failed. Please try again later.");
        }
        
        return $this->conn;
    }
}
?>
