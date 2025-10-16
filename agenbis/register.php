<?php
require_once "config/Database.php";
require_once "controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);

if($auth->isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$message = '';
$error = '';

if($_POST) {
    if($_POST['password'] !== $_POST['confirm_password']) {
        $error = 'Password dan konfirmasi password tidak sama';
    } else {
        $result = $auth->register($_POST);
        if($result['success']) {
            $message = $result['message'];
            header("refresh:2;url=login.php");
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .register-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    </style>
</head>

<body class="register-bg">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-bus text-4xl text-purple-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Daftar Akun Baru
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="login.php" class="font-medium text-purple-600 hover:text-purple-500">
                        masuk di sini
                    </a>
                </p>
            </div>

            <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= $error ?></span>
            </div>
            <?php endif; ?>

            <?php if($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= $message ?></span>
            </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <div class="mt-1 relative">
                            <input id="nama" name="nama" type="text" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="masukkan nama lengkap">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <div class="mt-1 relative">
                            <input id="email" name="email" type="email" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="masukkan email">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <div class="mt-1 relative">
                            <input id="telepon" name="telepon" type="tel" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="masukkan nomor telepon">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <div class="mt-1">
                            <textarea id="alamat" name="alamat" rows="3" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="masukkan alamat lengkap"></textarea>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative">
                            <input id="password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="buat password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi
                            Password</label>
                        <div class="mt-1 relative">
                            <input id="confirm_password" name="confirm_password" type="password" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="ulangi password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="agree_terms" name="agree_terms" type="checkbox" required
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="agree_terms" class="ml-2 block text-sm text-gray-900">
                        Saya menyetujui
                        <a href="#" class="text-purple-600 hover:text-purple-500">syarat dan ketentuan</a>
                    </label>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-purple-300 group-hover:text-purple-400"></i>
                        </span>
                        Daftar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Password tidak sama");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    confirmPassword.onkeyup = validatePassword;
    </script>
</body>

</html>