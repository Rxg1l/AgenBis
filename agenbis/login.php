<?php
require_once "config/Database.php";
require_once "controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);

if($auth->isLoggedIn()) {
    if($auth->isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit;
}

$error = '';
if($_POST) {
    $result = $auth->login($_POST['email'], $_POST['password']);
    if($result['success']) {
        if($result['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .login-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    </style>
</head>

<body class="login-bg">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-xl p-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-bus text-4xl text-purple-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Masuk ke Akun Anda
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Atau
                    <a href="register.php" class="font-medium text-purple-600 hover:text-purple-500">
                        daftar akun baru
                    </a>
                </p>
            </div>

            <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?= $error ?></span>
            </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <div class="mt-1 relative">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="masukkan email anda">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative">
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="masukkan password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox"
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Ingat saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-purple-300 group-hover:text-purple-400"></i>
                        </span>
                        Masuk
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="register.php" class="font-medium text-purple-600 hover:text-purple-500">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Demo Accounts</span>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-gray-100 p-2 rounded">
                        <strong>Admin:</strong><br>
                        admin@agenbis.com<br>
                        password
                    </div>
                    <div class="bg-gray-100 p-2 rounded">
                        <strong>User:</strong><br>
                        budi@email.com<br>
                        password
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>