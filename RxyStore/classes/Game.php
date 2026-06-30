<?php
require_once __DIR__ . '/../config/Database.php';

class Game {
    private $conn;
    private $table_games = "master.games";
    private $table_packages = "master.packages";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Mengambil semua game berdasarkan kategori filternya
    public function getAll($category = 'all') {
        if ($category == 'all') {
            $query = "SELECT * FROM " . $this->table_games . " ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
        } else {
            $query = "SELECT * FROM " . $this->table_games . " WHERE category = :category ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":category", $category);
        }
        $stmt->execute();
        return $stmt;
    }

    // Mengambil detail satu game berdasarkan slug URL-nya
    public function getBySlug($slug) {
        $query = "SELECT * FROM " . $this->table_games . " WHERE slug = :slug";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":slug", $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mengambil daftar paket harga voucher milik game tertentu
    public function getPackages($game_id) {
        $query = "SELECT * FROM " . $this->table_packages . " WHERE game_id = :game_id ORDER BY price ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":game_id", $game_id);
        $stmt->execute();
        return $stmt;
    }

    // Menyimpan data game baru dan mengembalikan ID-nya (Khusus PostgreSQL)
    public function create($slug, $name, $category, $developer, $image_path, $id_label, $id_placeholder, $currency) {
        try {
            // Menggunakan RETURNING id di akhir query khusus PostgreSQL
            $query = "INSERT INTO " . $this->table_games . " 
                      (slug, name, category, developer, image_path, id_label, id_placeholder, currency) 
                      VALUES (:slug, :name, :category, :developer, :image_path, :id_label, :id_placeholder, :currency)
                      RETURNING id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":slug", $slug);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":category", $category);
            $stmt->bindParam(":developer", $developer);
            $stmt->bindParam(":image_path", $image_path);
            $stmt->bindParam(":id_label", $id_label);
            $stmt->bindParam(":id_placeholder", $id_placeholder);
            $stmt->bindParam(":currency", $currency);
            
            $stmt->execute();
            
            // Ambil ID yang dihasilkan oleh database
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id']; // Mengembalikan angka ID game baru
            
        } catch (PDOException $e) {
            return false;
        }
    }

    // Menyimpan paket nominal & harga baru ke database lewat web Admin
    public function createPackage($game_id, $nominal, $price) {
        try {
            $query = "INSERT INTO " . $this->table_packages . " (game_id, nominal, price) VALUES (:game_id, :nominal, :price)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":game_id", $game_id);
            $stmt->bindParam(":nominal", $nominal);
            $stmt->bindParam(":price", $price);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}