<?php
require_once "config/Database.php";
require_once "models/Jadwal.php";
require_once "models/Rute.php";
require_once "controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);

$jadwal = new Jadwal($db);
$rute = new Rute($db);

// Get search parameters
$kota_asal = $_GET['asal'] ?? '';
$kota_tujuan = $_GET['tujuan'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

// Perform search if parameters exist
if(!empty($kota_asal) || !empty($kota_tujuan) || !empty($tanggal)) {
    $hasil_pencarian = $jadwal->search($kota_asal, $kota_tujuan, $tanggal);
} else {
    $hasil_pencarian = $jadwal->readAll();
}

// Get unique cities for dropdowns
$kota_kota = $rute->getAllCities();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Jadwal - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-bus text-2xl text-purple-600 mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">AgenBis</span>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-purple-600">Beranda</a>
                    <a href="jadwal.php" class="text-purple-600 font-medium">Jadwal</a>
                    <?php if($auth->isLoggedIn()): ?>
                    <?php if($auth->isAdmin()): ?>
                    <a href="admin/dashboard.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                        Dashboard Admin
                    </a>
                    <?php else: ?>
                    <a href="user/dashboard.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                        Dashboard
                    </a>
                    <?php endif; ?>
                    <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-purple-600">Login</a>
                    <a href="register.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                        Daftar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Cari Jadwal Bus</h1>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kota Asal</label>
                    <div class="relative">
                        <input type="text" name="asal" value="<?= htmlspecialchars($kota_asal) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                            placeholder="Kota asal" list="kota-asal">
                        <datalist id="kota-asal">
                            <?php if($kota_kota): foreach($kota_kota as $kota): ?>
                            <option value="<?= htmlspecialchars($kota) ?>">
                                <?php endforeach; endif; ?>
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kota Tujuan</label>
                    <div class="relative">
                        <input type="text" name="tujuan" value="<?= htmlspecialchars($kota_tujuan) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                            placeholder="Kota tujuan" list="kota-tujuan">
                        <datalist id="kota-tujuan">
                            <?php if($kota_kota): foreach($kota_kota as $kota): ?>
                            <option value="<?= htmlspecialchars($kota) ?>">
                                <?php endforeach; endif; ?>
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Keberangkatan</label>
                    <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                        min="<?= date('Y-m-d') ?>">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition font-bold">
                        <i class="fas fa-search mr-2"></i>Cari Jadwal
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    <?php if(!empty($kota_asal) || !empty($kota_tujuan) || !empty($tanggal)): ?>
                    Hasil Pencarian
                    <?php else: ?>
                    Semua Jadwal Tersedia
                    <?php endif; ?>
                </h2>
            </div>

            <div class="p-6">
                <?php if($hasil_pencarian && $hasil_pencarian->rowCount() > 0): ?>
                <div class="space-y-4">
                    <?php while($row = $hasil_pencarian->fetch()): ?>
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">
                                            <?= htmlspecialchars($row['kota_asal'] ?? '') ?> →
                                            <?= htmlspecialchars($row['kota_tujuan'] ?? '') ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <?= htmlspecialchars($row['nama_tipe'] ?? '') ?> •
                                            <?= htmlspecialchars($row['model'] ?? '') ?>
                                            (<?= htmlspecialchars($row['plat_nomor'] ?? '') ?>)</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-purple-600">
                                            Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.') ?>
                                        </p>
                                        <p class="text-sm text-gray-600">per orang</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-calendar-alt mr-3 text-purple-500"></i>
                                        <div>
                                            <p class="text-sm">Tanggal</p>
                                            <p class="font-semibold">
                                                <?= date('d M Y', strtotime($row['waktu_keberangkatan'])) ?></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-clock mr-3 text-purple-500"></i>
                                        <div>
                                            <p class="text-sm">Waktu</p>
                                            <p class="font-semibold">
                                                <?= date('H:i', strtotime($row['waktu_keberangkatan'])) ?> -
                                                <?= date('H:i', strtotime($row['waktu_tiba'])) ?></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-chair mr-3 text-purple-500"></i>
                                        <div>
                                            <p class="text-sm">Kursi Tersedia</p>
                                            <p class="font-semibold"><?= $row['kursi_tersedia'] ?? 0 ?> kursi</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-road mr-2"></i>
                                    <span>Jarak: <?= $row['jarak_km'] ?? 0 ?> km • Estimasi:
                                        <?= $row['waktu_tempuh_jam'] ?? 0 ?> jam</span>
                                </div>
                            </div>

                            <div class="mt-4 lg:mt-0 lg:ml-6">
                                <?php if($auth->isLoggedIn() && !$auth->isAdmin()): ?>
                                <a href="user/pesan.php?id=<?= $row['id'] ?>"
                                    class="block w-full bg-purple-600 text-white text-center py-3 px-6 rounded-lg hover:bg-purple-700 transition font-bold">
                                    <i class="fas fa-ticket-alt mr-2"></i>Pesan Sekarang
                                </a>
                                <?php elseif($auth->isLoggedIn() && $auth->isAdmin()): ?>
                                <button disabled
                                    class="block w-full bg-gray-400 text-white text-center py-3 px-6 rounded-lg font-bold cursor-not-allowed">
                                    <i class="fas fa-info-circle mr-2"></i>Admin Mode
                                </button>
                                <?php else: ?>
                                <a href="login.php"
                                    class="block w-full bg-purple-600 text-white text-center py-3 px-6 rounded-lg hover:bg-purple-700 transition font-bold">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Pesan
                                </a>
                                <?php endif; ?>

                                <p class="text-xs text-gray-500 text-center mt-2">
                                    <?= $row['kursi_tersedia'] ?? 0 ?> kursi tersedia
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak ada jadwal ditemukan</h3>
                    <p class="text-gray-500 mb-4">Coba ubah kriteria pencarian Anda</p>
                    <a href="jadwal.php"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        Tampilkan Semua Jadwal
                    </a>
                </div>
                <?php endif; ?>
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
    // Set min date untuk input tanggal
    document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
    </script>

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

    // Set min date untuk input tanggal
    document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>

</html>