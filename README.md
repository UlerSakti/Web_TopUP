# RxyStore - Data-Driven E-Commerce Top-Up System 🎮

![PHP](https://img.shields.io/badge/PHP-8.x_Native-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Environment](https://img.shields.io/badge/Environment-Laragon_/_Win11-0078D4?style=for-the-badge&logo=windows&logoColor=white)

**RxyStore** adalah aplikasi web e-commerce berbasis *Data-Driven Application* yang dirancang khusus untuk memproses layanan isi ulang (*top-up*) *voucher game* digital secara dinamis. Proyek ini dibangun untuk mentransformasi arsitektur aplikasi statis (*hardcoded*) menjadi sistem dinamis modular menggunakan paradigma **Pemrograman Berorientasi Objek (OOP)** pada PHP murni, gerbang koneksi aman **PDO Driver**, dan performa handal **RDBMS PostgreSQL**.

---

## 🚀 Fitur Utama Sistem

### 👤 Sisi Pelanggan (Guest & User Terdaftar)
* **Dynamic Landing Page:** Katalog etalase *game* di-render secara dinamis langsung dari repositori basis data berdasarkan pengondisian parameter URL GET `?game=slug`.
* **Flexible Transaction Form:** Form input data target ID akun menyesuaikan konfigurasi atribut *game* yang dipilih secara otomatis (*custom placeholder & label*).
* **Guest Checkout Support:** Mendukung pemesanan transaksi langsung bagi pelanggan anonim tanpa akun melalui arsitektur *Nullable Foreign Key*.
* **Personal Transaction Log:** Fitur pencatatan riwayat nota belanja khusus bagi *User* terdaftar menggunakan enkapsulasi state `$_SESSION`.

### 🎛️ Sisi Administrator (Admin Dashboard Panel)
* **One-to-Many Dynamic Form:** Formulir mutakhir untuk mendaftarkan *game* baru, mengunggah *cover* gambar, sekaligus menyisipkan puluhan varian paket harga voucher secara simultan dalam sekali klik.
* **Secure File Handling:** Pengamanan berkas fisik aset multimedia menggunakan teknik penanganan waktu (`time()`) untuk mencegah insiden tumpang-tindih berkas (*file overwriting*).
* **Comprehensive Order Management:** Panel kendali terpusat berbasis kueri `LEFT JOIN` untuk memantau antrean transaksional dan memperbarui status pembayaran (*Pending, Success, Failed*).

---

## 📂 Struktur Direktori Proyek

Aplikasi ini menerapkan prinsip *Separation of Concerns* (SoC) melalui pembagian struktur berkas modular sebagai berikut:

```text
VALO_TOPUP/
│
├── assets/             # Pusat penyimpanan file gambar cover produk (.jpg, .png)
├── auth/               # Modul autentikasi (login.php, register.php, logout.php)
├── classes/            # Core Logic Layer (User.php, Transaction.php, Game.php)
├── config/             # Environment Gateway (Database.php dengan Driver PDO)
├── dashboard/          # Operasional Web (index.php, admin.php, add_game.php)
├── includes/           # Reusable UI Components (sidebar.php master navigasi)
└── index.php           # Public Landing Page (Etalase Utama Platform)

💾 Skema Fisik Basis Data (SQL DDL)
Gunakan struktur skema tabel relasional berikut di PostgreSQL Anda sebelum menjalankan aplikasi:
-- Create Schema Area
CREATE SCHEMA IF NOT EXISTS master;

-- 1. Tabel Users
CREATE TABLE master.users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(10) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Games
CREATE TABLE master.games (
    id SERIAL PRIMARY KEY,
    slug VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(20) NOT NULL,
    developer VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    id_label VARCHAR(100) NOT NULL,
    id_placeholder VARCHAR(255) NOT NULL,
    currency VARCHAR(20) NOT NULL
);

-- 3. Tabel Packages
CREATE TABLE master.packages (
    id SERIAL PRIMARY KEY,
    game_id INT REFERENCES master.games(id) ON DELETE CASCADE,
    nominal INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- 4. Tabel Transactions
CREATE TABLE master.transactions (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES master.users(id) ON DELETE SET NULL,
    riot_id VARCHAR(100) NOT NULL,
    nominal INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status VARCHAR(15) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

⚙️ Panduan Instalasi Lokal
1. Kloning Repositori Proyek
Buka Git Bash atau Command Prompt, masuk ke direktori www/ Laragon Anda, lalu jalankan perintah:
cd C:\laragon\www
git clone [https://github.com/UlerSakti/Web_TopUP.git](https://github.com/UlerSakti/Web_TopUP.git)
cd Web_TopUP

2. Konfigurasi Server PostgreSQL
Jalankan PostgreSQL melalui aplikasi pgAdmin 4 atau DBeaver.

Buat database baru dengan nama Topup_VP.

Buka SQL Editor, salin skrip DDL pada poin tabel di atas, lalu klik Execute/Run.

3. Sinkronisasi Kredensial Database
Buka file config/Database.php menggunakan VS Code, sesuaikan properti hak akses PostgreSQL lokal Anda:
private $host = "localhost";
private $db_name = "Topup_VP"; 
private $username = ""; // Isi dengan username postgres Anda    
private $password = "";    // Isi dengan password database Anda

4. Menjalankan Aplikasi
Buka aplikasi kontrol panel Laragon, klik tombol Start All.

Akses aplikasi melalui web peramban favorit Anda dengan mengetikkan URL alamat:

http://localhost/Web_TopUP/ atau http://web-topup.test/

👥 Kontributor & Hak Cipta
Penyusun: M. Raditya Zauhair (NIM: 2555200012)

Afiliasi: Program Studi Teknik Informatika, Fakultas Sains dan Teknologi, Universitas PGRI Delta Sidoarjo (UNIPDA).

Proyek laporan akhir praktikum mandiri ini dilindungi hak cipta akademik. Dibuat untuk tujuan pemenuhan komponen penilaian mata kuliah Pemrograman Web 2026.
---
