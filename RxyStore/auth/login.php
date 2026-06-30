<?php
require_once '../classes/User.php';
session_start();

// 1. Jika user sudah login sebelumnya dan mencoba buka login.php, 
// arahkan sesuai dengan role-nya
if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] === 'admin') {
        header("Location: ../dashboard/admin.php");
    } else {
        header("Location: ../dashboard/index.php");
    }
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User();
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 2. Jika proses login berhasil divalidasi oleh database
    if ($user->login($username, $password)) {
        
        // Cek role dari session yang baru saja dibuat di class User
        if ($_SESSION['role'] === 'admin') {
            header("Location: ../dashboard/admin.php"); // Ke halaman Admin
        } else {
            header("Location: ../dashboard/index.php"); // Ke halaman User biasa
        }
        exit;

    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - ValoStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { valoRed: '#ff4655', valoDark: '#111111', valoDarker: '#0f1923' } } }
        }
    </script>
</head>
<body class="bg-valoDark text-white h-screen flex items-center justify-center">
    <div class="bg-valoDarker p-8 rounded-lg shadow-lg w-96 border border-gray-800">
        <h2 class="text-3xl font-bold mb-6 text-center italic tracking-wider text-valoRed">LOGIN</h2>
        
        <?php if($error): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-sm text-center"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">Username</label>
                <input type="text" name="username" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed">
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required class="w-full p-3 bg-valoDark border border-gray-700 rounded text-white focus:outline-none focus:border-valoRed">
            </div>
            <button type="submit" class="w-full bg-valoRed hover:bg-red-600 text-white font-bold py-3 rounded transition">Login</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-400">Belum punya akun? <a href="register.php" class="text-valoRed hover:underline">Daftar di sini</a></p>
    </div>
</body>
</html>