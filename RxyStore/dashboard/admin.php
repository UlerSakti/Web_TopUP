<?php
session_start();
require_once '../classes/Transaction.php';

// Proteksi Halaman: Lempar ke index jika bukan admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../index.php");
    exit;
}

$transaction = new Transaction();

// Proses UPDATE Status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action'];
    
    if($status == 'success' || $status == 'failed') {
        $transaction->updateStatus($id, $status);
        header("Location: admin.php"); // Refresh halaman
        exit;
    }
}

// Ambil semua data transaksi
$all_transactions = $transaction->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - RxyStore</title>
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
                <h2 class="text-3xl font-bold">Panel Admin</h2>
                <p class="text-gray-400 mt-1">Kelola pesanan top up yang masuk dari User maupun Guest.</p>
            </header>

            <div class="bg-valoDarker p-6 rounded-lg border border-gray-800 overflow-x-auto shadow-lg">
                <h2 class="text-xl font-bold mb-4 border-l-4 border-valoRed pl-2">Kelola Semua Transaksi</h2>
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="border-b border-gray-700 text-gray-400">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Pelanggan</th>
                            <th class="p-3">ID Target</th>
                            <th class="p-3">Nominal (Harga)</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($all_transactions->rowCount() > 0): ?>
                            <?php while ($row = $all_transactions->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="border-b border-gray-800 hover:bg-gray-800 transition">
                                    <td class="p-3 text-sm text-gray-300"><?= date('d M, H:i', strtotime($row['created_at'])) ?></td>
                                    <td class="p-3">
                                        <?php if($row['username']): ?>
                                            <span class="text-blue-400 font-bold">@<?= htmlspecialchars($row['username']) ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-500 italic font-bold">Guest</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-sm tracking-wider"><?= htmlspecialchars($row['riot_id']) ?></td>
                                    <td class="p-3">
                                        <span class="font-bold text-valoRed"><?= $row['nominal'] ?> Points</span> <br> 
                                        <span class="text-xs text-gray-400">Rp <?= number_format($row['price'], 0, ',', '.') ?></span>
                                    </td>
                                    <td class="p-3">
                                        <?php if($row['status'] == 'pending'): ?>
                                            <span class="px-2 py-1 border border-yellow-600 text-yellow-500 text-xs rounded">Pending</span>
                                        <?php elseif($row['status'] == 'success'): ?>
                                            <span class="px-2 py-1 border border-green-600 text-green-500 text-xs rounded">Success</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 border border-red-600 text-red-500 text-xs rounded">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3">
                                        <?php if($row['status'] == 'pending'): ?>
                                            <div class="flex gap-2">
                                                <a href="?action=success&id=<?= $row['id'] ?>" class="px-3 py-1 bg-green-600/20 border border-green-600 text-green-500 hover:bg-green-600 hover:text-white text-xs rounded font-bold transition">Selesai</a>
                                                <a href="?action=failed&id=<?= $row['id'] ?>" class="px-3 py-1 bg-red-600/20 border border-red-600 text-red-500 hover:bg-red-600 hover:text-white text-xs rounded font-bold transition">Tolak</a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-600 text-xs italic">Sudah diproses</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-5 text-center text-gray-500">Belum ada pesanan masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

    </div>
</body>
</html>