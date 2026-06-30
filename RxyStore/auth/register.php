<?php
require_once '../classes/User.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User();
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($user->register($username, $password)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
    } else {
        $message = "Username sudah digunakan atau terjadi kesalahan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - ValoStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { valoRed: '#ff4655', valoDark: '#111111', valoDarker: '#0f1923' } } }
        }
    </script>
</head>
<body class="bg-valoDark text-white h-screen flex items-center justify-center">
    <div class="bg-valoDarker p-8 rounded-lg shadow-lg w-96 border border-gray-800">
        <h2 class="text-3xl font-bold mb-6 text-center italic tracking-wider text-valoRed">REGISTER</h2>
        
        <?php if($message): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-sm text-center"><?= $message ?></div>
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
            <button type="submit" class="w-full bg-valoRed hover:bg-red-600 text-white font-bold py-3 rounded transition">Daftar Sekarang</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-400">Sudah punya akun? <a href="login.php" class="text-valoRed hover:underline">Login di sini</a></p>
    </div>
</body>
</html>