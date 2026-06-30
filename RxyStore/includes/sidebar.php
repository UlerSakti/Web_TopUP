<?php
require_once __DIR__ . '/../classes/Game.php';
$gameModel = new Game();

$prefix = isset($path_prefix) ? $path_prefix : './';
$current_page = isset($page_type) ? $page_type : 'landing';
$current_game = isset($_GET['game']) ? $_GET['game'] : '';
$current_cat = isset($_GET['category']) ? $_GET['category'] : 'all';
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : 'Guest';
$role = $is_logged_in ? $_SESSION['role'] : 'user';
?>

<div class="md:hidden flex items-center justify-between p-4 bg-valoDarker border-b border-gray-800 w-full shrink-0 z-50">
    <h1 class="text-2xl font-bold italic tracking-wider text-valoRed">VALO<span class="text-white">STORE</span></h1>
    <button id="menu-btn" class="text-gray-400 hover:text-white focus:outline-none">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>
</div>

<aside id="sidebar" class="hidden md:flex w-full md:w-64 bg-valoDarker md:border-r border-gray-800 flex-col justify-between shrink-0 absolute md:relative z-40 h-[calc(100vh-73px)] md:h-screen top-[73px] md:top-0 overflow-y-auto">
    <div>
        <div class="hidden md:block p-6 border-b border-gray-800">
            <h1 class="text-3xl font-bold italic tracking-wider text-valoRed">VALO<span class="text-white">STORE</span></h1>
        </div>

        <nav class="p-4 space-y-2">
            <p class="text-xs text-gray-500 font-bold mb-2 uppercase tracking-wider">
                <?= ($current_page == 'admin') ? 'Menu Admin' : 'Navigasi Sistem' ?>
            </p>
            
            <?php if ($current_page == 'landing'): ?>
                <a href="<?= $prefix ?>index.php?category=all" class="block px-4 py-3 <?= ($current_cat == 'all') ? 'bg-valoRed text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> rounded font-bold transition">Semua Game</a>
                <a href="index.php?category=pc" class="block px-4 py-3 <?= ($current_cat == 'pc') ? 'bg-valoRed text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> rounded transition font-bold">PC Game</a>
                <a href="index.php?category=mobile" class="block px-4 py-3 <?= ($current_cat == 'mobile') ? 'bg-valoRed text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> rounded transition font-bold">Mobile Game</a>
                <a href="#about" class="block px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded transition font-bold">Tentang Kami</a>
            
            <?php elseif ($current_page == 'admin'): ?>
                <?php $active_file = basename($_SERVER['PHP_SELF']); ?>
                
                <a href="<?= $prefix ?>dashboard/admin.php" class="block px-4 py-3 <?= ($active_file == 'admin.php') ? 'bg-valoRed text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> rounded font-bold transition">Kelola Transaksi</a>
                
                <a href="<?= $prefix ?>dashboard/add_game.php" class="block px-4 py-3 <?= ($active_file == 'add_game.php') ? 'bg-valoRed text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> rounded font-bold transition">Tambah Game Baru</a>
                
                <a href="<?= $prefix ?>index.php" class="block px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded transition border border-transparent">Kembali ke Katalog</a>
            
            <?php else: ?>
                <?php 
                $list_games = $gameModel->getAll();
                while ($g = $list_games->fetch(PDO::FETCH_ASSOC)): 
                ?>
                    <a href="<?= $prefix ?>dashboard/index.php?game=<?= $g['slug'] ?>" class="block px-4 py-3 <?= ($current_game == $g['slug']) ? 'bg-valoRed text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> rounded font-bold transition">Top Up <?= $g['name'] ?></a>
                <?php endwhile; ?>
                <a href="<?= $prefix ?>index.php" class="block px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded transition border border-transparent">Katalog Game</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="p-5 border-t border-gray-800 bg-gray-900/30">
        <p class="text-sm text-gray-400 mb-1">Status Anda:</p>
        <p class="font-bold text-lg mb-4 text-white">@<?= $username ?></p>
        
        <?php if($is_logged_in): ?>
            <a href="<?= $prefix ?>auth/logout.php" class="block w-full text-center px-4 py-2 border border-red-600 text-red-500 hover:bg-red-600 hover:text-white font-bold rounded transition">Logout</a>
        <?php else: ?>
            <a href="<?= $prefix ?>auth/login.php" class="block w-full text-center px-4 py-2 bg-valoRed hover:bg-red-600 text-white font-bold rounded transition mb-2">Login</a>
        <?php endif; ?>
    </div>
</aside>

<script>
    document.getElementById('menu-btn').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('flex');
    });
</script>