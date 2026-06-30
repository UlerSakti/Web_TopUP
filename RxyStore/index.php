<?php
session_start();
require_once 'classes/Game.php';

$gameModel = new Game();
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Ambil data game terfilter dari database
$stmt_games = $gameModel->getAll($category);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Top Up - ValoStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { valoRed: '#ff4655', valoDark: '#111111', valoDarker: '#0f1923' } } } }
    </script>
</head>
<body class="bg-valoDark text-white font-sans antialiased overflow-hidden">

    <div class="flex flex-col md:flex-row h-screen w-full relative">
        
        <?php 
        $path_prefix = './'; 
        $page_type = 'landing'; 
        include 'includes/sidebar.php'; 
        ?>

        <main class="flex-1 overflow-y-auto w-full relative bg-valoDark scroll-smooth">
            <div class="bg-valoDarker pt-16 pb-16 border-b border-gray-800 relative w-full">
                <div class="container mx-auto px-6 text-center relative z-10">
                    <h2 class="text-4xl md:text-5xl font-extrabold mb-4 text-white tracking-tight uppercase">
                        <?= ($category == 'all') ? 'Semua Game' : $category . ' Game' ?>
                    </h2>
                    <p class="text-gray-400 text-lg">Temuin game favorit kamu di sini</p>
                </div>
            </div>

            <div class="container mx-auto px-6 mt-12 relative z-20 max-w-3xl">
                <div class="grid grid-cols-2 gap-8">
                    
                    <?php 
                    if($stmt_games->rowCount() > 0):
                        while ($row = $stmt_games->fetch(PDO::FETCH_ASSOC)): 
                    ?>
                        <div class="group">
                            <a href="dashboard/index.php?game=<?= $row['slug'] ?>" class="block relative rounded-2xl overflow-hidden shadow-xl border border-transparent group-hover:border-valoRed transition duration-300 bg-gray-900">
                                <img src="<?= $row['image_path'] ?>" alt="<?= $row['name'] ?>" class="w-full aspect-square object-cover group-hover:scale-110 transition duration-500">
                                <div class="absolute bottom-0 w-full bg-valoRed text-white text-xs font-bold py-2 text-center">
                                    PROMO TOP UP
                                </div>
                            </a>
                            <div class="mt-3 px-1">
                                <h3 class="text-sm md:text-base font-bold text-white leading-tight group-hover:text-valoRed transition"><?= $row['name'] ?></h3>
                                <p class="text-xs text-gray-500 mt-1"><?= $row['developer'] ?></p>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <p class="col-span-2 text-center text-gray-500">Belum ada game untuk kategori ini.</p>
                    <?php endif; ?>

                </div>
            </div>

            <div id="about" class="container mx-auto px-6 mt-20 mb-20 max-w-3xl border-t border-gray-800 pt-16">
                <h3 class="text-2xl font-bold mb-6 border-l-4 border-valoRed pl-3 text-white">Tentang ValoStore</h3>
                <p class="text-gray-400 leading-relaxed mb-10 text-justify">
                    ValoStore adalah platform penyedia layanan top up game favoritmu. Kami berkomitmen untuk memberikan pengalaman belanja digital yang cepat, aman, dan terpercaya.
                </p>
            </div>
            
        </main>
    </div>

</body>
</html>