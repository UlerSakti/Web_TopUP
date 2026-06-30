<?php
require_once '../config/Database.php';

class User {
    private $conn;
    private $table_name = "master.users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Method untuk Register
    public function register($username, $password) {
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(":username", $username);
        $check_stmt->execute();

        if($check_stmt->rowCount() > 0) {
            return false; 
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO " . $this->table_name . " (username, password, role) VALUES (:username, :password, 'user')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Method untuk Login
    public function login($username, $password) {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                return true;
            }
        }
        return false;
    }
}
?>