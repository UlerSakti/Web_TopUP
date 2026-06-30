<?php
session_start();
require_once '../classes/Game.php';

// PROTEKSI KEAMANAN: Hanya mengizinkan user dengan role admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../index.php");
    exit;
}

$gameModel = new Game();
$message = "";

// Memproses inputan ketika tombol "Daftarkan Game & Paket" ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_game'])) {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug']))); 
    $category = $_POST['category'];
    $developer = $_POST['developer'];
    $id_label = $_POST['id_label'];
    $id_placeholder = $_POST['id_placeholder'];
    $currency = $_POST['currency'];
    
    $image_path = "";

    // 1. PROSES UPLOAD GAMBAR COVER
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/'; 
        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_upload_path = $upload_dir . $file_name;
        $db_image_path = 'assets/' . $file_name; 

        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_upload_path)) {
            $image_path = $db_image_path;
        } else {
            $message = "<div class='bg-red-500/20 border border-red-500 text-red-400 p-3 rounded mb-6 font-bold'>Gagal mengunggah file gambar ke folder assets.</div>";
        }
    }

    // 2. PROSES SIMPAN DATA DATA GAME & DAFTAR PAKETNYA (TRANSAKSIONAL)
    if ($image_path !== "") {
        // Panggil method create yang mengembalikan ID Game baru
        $new_game_id = $gameModel->create($slug, $name, $category, $developer, $image_path, $id_label, $id_placeholder, $currency);

        if ($new_game_id) {
            // Ambil data array paket dari inputan form text HTML
            $nominals = isset($_POST['nominals']) ? $_POST['nominals'] : [];
            $prices = isset($_POST['prices']) ? $_POST['prices'] : [];
            $success_packages = 0;

            // Lakukan perulangan looping untuk menyimpan setiap baris paket harga
            for ($i = 0; $i < count($nominals); $i++) {
                $nominal = intval($nominals[$i]);
                $price = floatval($prices[$i]);

                if ($nominal > 0 && $price > 0) {
                    if ($gameModel->createPackage($new_game_id, $nominal, $price)) {
                        $success_packages++;
                    }
                }
            }

            $message = "<div class='bg-green-500/20 border border-green-500 text-green-400 p-3 rounded mb-6 font-bold'>Sukses! Game baru berhasil dibuat dan sebanyak " . $success_packages . " paket harga awal langsung didaftarkan!</div>";
        } else {
            $message = "<div class='bg-red-500/20 border border-red-500 text-red-400 p-3 rounded mb-6 font-bold'>Gagal menambahkan game. Pastikan slug URL belum pernah digunakan sebelumnya.</div>";
        }
    } else if (empty($message)) {
        $message = "<div class='bg-yellow-500/20 border border-yellow-500 text-yellow-400 p-3 rounded mb-6 font-bold'>Harap lampirkan file gambar cover game terlebih dahulu.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Game & Paket - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { valoRed: '#ff4655', valoDark: '#111111', valoDarker: '#0f1923' } } } }
    </script>
</head>
<body class="bg-valoDark text-white font-sans antialiased">

    <div class="flex flex-col md:flex-row h-screen overflow-hidden relative">

        <?php 
        $path_prefix = '../'; 
        $page_type = 'admin'; 
        include '../includes/sidebar.php'; 
        ?>

        <main class="flex-1 overflow-y-auto p-8 bg-valoDark">
            <header class="mb-8 border-b border-gray-800 pb-6">
                <h2 class="text-3xl font-bold">Tambah Game & Paket Sekaligus</h2>
                <p class="text-gray-400 mt-1">Satu formulir terintegrasi untuk mendaftarkan data komoditas game baru beserta varian harganya.</p>
            </header>

            <div class="max-w-4xl bg-valoDarker p-8 rounded-xl border border-gray-800 shadow-2xl mb-12">
                <?= $message ?>
                
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Nama Game</label>
                            <input type="text" name="name" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="Contoh: Mobile Legends">
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Slug URL</label>
                            <input type="text" name="slug" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="Contoh: mlbb">
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Kategori Platform</label>
                            <select name="category" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed">
                                <option value="mobile">Mobile Game</option>
                                <option value="pc">PC Game</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Developer</label>
                            <input type="text" name="developer" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="Contoh: Moonton">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Upload Gambar Cover Game</label>
                            <input type="file" name="image_file" accept="image/*" required 
                                   class="w-full p-3 bg-valoDark border border-gray-700 rounded text-gray-400 focus:outline-none focus:border-valoRed 
                                          file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-bold file:bg-valoRed file:text-white hover:file:bg-red-600 transition cursor-pointer">
                        </div>

                        <div class="md:col-span-2 border-t border-gray-800 pt-4">
                            <h3 class="text-sm font-bold text-valoRed uppercase tracking-widest">Konfigurasi Form Transaksi</h3>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Label Form Input ID</label>
                            <input type="text" name="id_label" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="Contoh: User ID & Zone ID">
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Mata Uang Game</label>
                            <input type="text" name="currency" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="Contoh: Diamonds">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-2 font-bold">Teks Contoh (Placeholder Input)</label>
                            <input type="text" name="id_placeholder" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="Contoh: Masukkan User ID (Zone ID) Anda">
                        </div>
                    </div>

                    <div class="border-t border-gray-800 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-valoRed uppercase tracking-widest">Input Varian Paket Harga</h3>
                            <button type="button" id="add-row-btn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold text-xs rounded transition flex items-center gap-1">
                                <span>+</span> Tambah Baris Paket
                            </button>
                        </div>

                        <div id="package-wrapper" class="space-y-3">
                            <div class="flex items-center gap-4 bg-valoDark p-3 rounded border border-gray-800 package-row">
                                <div class="flex-1 grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="number" name="nominals[]" required class="w-full p-2 bg-valoDarker border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-valoRed" placeholder="Jumlah Nominal (Contoh: 86)">
                                    </div>
                                    <div>
                                        <input type="number" name="prices[]" required class="w-full p-2 bg-valoDarker border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-valoRed" placeholder="Harga Rupiah (Contoh: 20000)">
                                    </div>
                                </div>
                                <button type="button" class="text-gray-500 cursor-not-allowed text-sm px-2" disabled> Utama </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-800">
                        <button type="submit" name="add_game" class="w-full bg-valoRed hover:bg-red-600 text-white font-bold py-4 rounded-lg transition shadow-[0_0_15px_rgba(255,70,85,0.4)]">Daftarkan Game & Seluruh Paket</button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('add-row-btn').addEventListener('click', function() {
            var wrapper = document.getElementById('package-wrapper');
            
            // Membuat elemen baris baru secara dinamis
            var newRow = document.createElement('div');
            newRow.className = 'flex items-center gap-4 bg-valoDark p-3 rounded border border-gray-800 package-row';
            
            newRow.innerHTML = `
                <div class="flex-1 grid grid-cols-2 gap-4">
                    <div>
                        <input type="number" name="nominals[]" required class="w-full p-2 bg-valoDarker border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-valoRed" placeholder="Jumlah Nominal">
                    </div>
                    <div>
                        <input type="number" name="prices[]" required class="w-full p-2 bg-valoDarker border border-gray-700 rounded text-white text-sm focus:outline-none focus:border-valoRed" placeholder="Harga Rupiah">
                    </div>
                </div>
                <button type="button" class="remove-row-btn text-red-500 hover:text-red-400 font-bold text-sm px-2 transition">Hapus</button>
            `;
            
            wrapper.appendChild(newRow);
            
            // Menambahkan fungsi hapus baris ketika tombol hapus diklik
            newRow.querySelector('.remove-row-btn').addEventListener('click', function() {
                newRow.remove();
            });
        });
    </script>
</body>
</html>