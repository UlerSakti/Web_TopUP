<?php
require_once '../config/Database.php';

class Transaction {
    private $conn;
    private $table_name = "master.transactions";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // CREATE: Menambah transaksi baru
    public function create($user_id, $riot_id, $nominal, $price) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, riot_id, nominal, price) VALUES (:user_id, :riot_id, :nominal, :price)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":riot_id", $riot_id);
        $stmt->bindParam(":nominal", $nominal);
        $stmt->bindParam(":price", $price);

        return $stmt->execute();
    }

    // READ: Menampilkan riwayat transaksi (berdasarkan user yang login)
    public function readByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }

    // DELETE: Membatalkan/menghapus transaksi (hanya saat masih pending)
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // READ ALL: Mengambil semua data transaksi (Khusus Admin)
    public function readAll() {
        $query = "SELECT t.*, u.username FROM " . $this->table_name . " t 
                  LEFT JOIN master.users u ON t.user_id = u.id 
                  ORDER BY t.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // UPDATE: Mengubah status transaksi (Khusus Admin)
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>