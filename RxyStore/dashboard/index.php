<?php
session_start();
require_once '../classes/Transaction.php';
require_once '../classes/Game.php';

$transaction = new Transaction();
$gameModel = new Game();

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$username = $is_logged_in ? $_SESSION['username'] : 'Guest';

$message = "";
$history = null;

// 1. Ambil slug game dari URL parameter
$game_slug = isset($_GET['game']) ? $_GET['game'] : 'valorant';

// 2. Ambil data spesifik game dari database secara dinamis
$game_data = $gameModel->getBySlug($game_slug);

// Proteksi jika user asal ketik nama game di URL
if (!$game_data) {
    header("Location: ../index.php");
    exit;
}

// 3. Ambil daftar paket harga voucher milik game tersebut dari database
$stmt_packages = $gameModel->getPackages($game_data['id']);

// Proses Insert Pemesanan (CREATE)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['topup'])) {
    $riot_id = $_POST['riot_id'];
    $package = explode("|", $_POST['package']);
    $nominal = $package[0];
    $price = $package[1];

    if ($transaction->create($user_id, $riot_id, $nominal, $price)) {
        $message = "<div class='bg-green-500/20 border border-green-500 text-green-400 p-3 rounded mb-4'>Pesanan Top Up berhasil dibuat!</div>";
    } else {
        $message = "<div class='bg-red-500/20 border border-red-500 text-red-400 p-3 rounded mb-4'>Gagal membuat pesanan.</div>";
    }
}

// Ambil data riwayat transaksi user
if ($is_logged_in) {
    $history = $transaction->readByUser($user_id);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Top Up <?= $game_data['name'] ?> - ValoStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { valoRed: '#ff4655', valoDark: '#111111', valoDarker: '#0f1923' } } } }
    </script>
</head>
<body class="bg-valoDark text-white font-sans antialiased">

    <div class="flex flex-col md:flex-row h-screen overflow-hidden relative">

        <?php 
        $path_prefix = '../'; 
        $page_type = 'dashboard'; 
        include '../includes/sidebar.php'; 
        ?>

        <main class="flex-1 overflow-y-auto p-8 bg-valoDark">
            <header class="mb-8 border-b border-gray-800 pb-6">
                <h2 class="text-3xl font-bold">Top Up <?= $game_data['name'] ?></h2>
                <p class="text-gray-400 mt-1">Lengkapi data di bawah untuk memproses pesanan Anda.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-valoDarker p-6 rounded-lg border border-gray-800 h-fit shadow-lg lg:col-span-1">
                    <h2 class="text-xl font-bold mb-4 border-l-4 border-valoRed pl-2">Detail Pesanan</h2>
                    <?= $message ?>
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="block text-sm text-gray-400 mb-2"><?= $game_data['id_label'] ?></label>
                            <input type="text" name="riot_id" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed" placeholder="<?= $game_data['id_placeholder'] ?>">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm text-gray-400 mb-2">Pilih Paket <?= $game_data['currency'] ?></label>
                            <select name="package" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed">
                                <?php while ($pack = $stmt_packages->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?= $pack['nominal'] ?>|<?= $pack['price'] ?>">
                                        <?= $pack['nominal'] ?> <?= $game_data['currency'] ?> - Rp <?= number_format($pack['price'], 0, ',', '.') ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" name="topup" class="w-full bg-valoRed hover:bg-red-600 text-white font-bold py-3 rounded transition">Bayar Sekarang</button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-valoDarker p-6 rounded-lg border border-gray-800 overflow-x-auto shadow-lg">
                    <h2 class="text-xl font-bold mb-4 border-l-4 border-valoRed pl-2">Riwayat Transaksi</h2>
                    
                    <?php if(!$is_logged_in): ?>
                        <div class="flex flex-col items-center justify-center p-10 text-center border border-dashed border-gray-700 rounded-lg bg-gray-900/50">
                            <h3 class="text-lg font-bold text-gray-300">Anda Membeli Sebagai Tamu (Guest)</h3>
                            <p class="text-gray-500 mt-2 max-w-md text-sm">Untuk melihat riwayat lengkap, silakan login.</p>
                        </div>
                    <?php else: ?>
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="border-b border-gray-700 text-gray-400">
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">ID Target</th>
                                    <th class="p-3">Nominal</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($history && $history->rowCount() > 0): ?>
                                    <?php while ($row = $history->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr class="border-b border-gray-800 hover:bg-gray-800 transition">
                                            <td class="p-3"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                            <td class="p-3"><?= htmlspecialchars($row['riot_id']) ?></td>
                                            <td class="p-3 font-bold text-valoRed"><?= $row['nominal'] ?> Items</td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 border border-yellow-600 text-yellow-500 text-xs rounded"><?= $row['status'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="p-5 text-center text-gray-500">Belum ada transaksi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>
</html>