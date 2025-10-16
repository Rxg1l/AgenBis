<?php
require_once "../config/Database.php";
require_once "../models/User.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAuth();

$user = new User($db);
$user->id = $_SESSION['user_id'];
$user_data = $user->getProfile();

$success = '';
$error = '';

if($_POST) {
    $user->id = $_SESSION['user_id'];
    $user->nama = $_POST['nama'];
    $user->telepon = $_POST['telepon'];
    $user->alamat = $_POST['alamat'];
    
    // Handle photo upload
    if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $upload_dir = "../assets/images/profiles/";
        $file_extension = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
        $upload_file = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_file)) {
            $user->foto_profil = 'profiles/' . $filename;
            $_SESSION['user_foto'] = $user->foto_profil;
        }
    }
    
    if($user->updateProfile()) {
        $_SESSION['user_nama'] = $user->nama;
        $success = "Profil berhasil diperbarui";
        $user_data = $user->getProfile(); // Refresh data
    } else {
        $error = "Gagal memperbarui profil";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Navigation - Sama seperti di dashboard -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-bus text-2xl text-purple-600 mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">AgenBis</span>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-600 hover:text-purple-600">Beranda</a>
                    <a href="../jadwal.php" class="text-gray-600 hover:text-purple-600">Jadwal</a>

                    <!-- User Menu dengan Logout -->
                    <div class="relative group">
                        <button
                            class="flex items-center space-x-2 text-gray-700 hover:text-purple-600 bg-gray-100 px-3 py-2 rounded-lg">
                            <img class="w-8 h-8 rounded-full"
                                src="<?= $user_data['foto_profil'] ? '../assets/images/'.$user_data['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($user_data['nama']).'&background=purple&color=white' ?>"
                                alt="<?= $user_data['nama'] ?>">
                            <span><?= $user_data['nama'] ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div
                            class="absolute right-0 w-48 mt-2 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <a href="dashboard.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 border-b">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="profile.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 border-b">
                                <i class="fas fa-user mr-2"></i>Profil Saya
                            </a>
                            <a href="../logout.php"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Profil Saya</h1>
            <p class="text-gray-600">Kelola informasi profil Anda</p>
        </div>

        <?php if($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $success ?>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Profil</h2>

                <div class="text-center mb-6">
                    <div class="relative inline-block">
                        <img class="w-32 h-32 rounded-full mx-auto border-4 border-purple-200"
                            src="<?= $user_data['foto_profil'] ? '../assets/images/'.$user_data['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($user_data['nama']).'&background=purple&color=white&size=128' ?>"
                            alt="<?= $user_data['nama'] ?>">
                        <div class="absolute bottom-0 right-0 bg-purple-600 rounded-full p-2">
                            <i class="fas fa-camera text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mt-4"><?= $user_data['nama'] ?></h3>
                    <p class="text-gray-600"><?= $user_data['email'] ?></p>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-user-tag mr-3 w-5"></i>
                        <span>Member sejak <?= date('M Y', strtotime($user_data['created_at'])) ?></span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone mr-3 w-5"></i>
                        <span><?= $user_data['telepon'] ?: 'Belum diisi' ?></span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt mr-3 w-5"></i>
                        <span><?= $user_data['alamat'] ?: 'Belum diisi' ?></span>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Profil</h2>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama" value="<?= htmlspecialchars($user_data['nama']) ?>"
                                        required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" value="<?= htmlspecialchars($user_data['email']) ?>" disabled
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-500">
                                    <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="tel" name="telepon" value="<?= htmlspecialchars($user_data['telepon']) ?>"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Contoh: 081234567890">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <textarea name="alamat" rows="3"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($user_data['alamat']) ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                                <div class="flex items-center space-x-4">
                                    <input type="file" name="foto_profil" accept="image/*"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <a href="dashboard.php"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Ubah Password</h2>

                    <form>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                                <input type="password"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <input type="password"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password
                                    Baru</label>
                                <input type="password"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <div class="flex justify-end">
                                <button type="button"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                                    Ubah Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 AgenBis. All rights reserved.</p>
        </div>
    </footer>

    <script>
    // Konfirmasi logout
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLinks = document.querySelectorAll('a[href*="logout.php"]');

        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Apakah Anda yakin ingin logout?')) {
                    e.preventDefault();
                }
            });
        });
    });
    </script>

</body>

</html>