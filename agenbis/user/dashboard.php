<?php
require_once "../config/Database.php";
require_once "../models/User.php";
require_once "../models/Pemesanan.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAuth();

$user = new User($db);
$user->id = $_SESSION['user_id'];
$user_data = $user->getProfile();

$pemesanan = new Pemesanan($db);
$riwayat_pemesanan = $pemesanan->readByUser($_SESSION['user_id']);

// Hitung statistik yang sebenarnya
$total_pemesanan = 0;
$tiket_aktif = 0;
$perjalanan_selesai = 0;

if ($riwayat_pemesanan) {
    while($row = $riwayat_pemesanan->fetch()) {
        $total_pemesanan++;
        if ($row['status_pembayaran'] == 'Success') {
            $tiket_aktif++;
        }
        // Untuk demo, anggap perjalanan selesai jika status success dan tanggal sudah lewat
        if ($row['status_pembayaran'] == 'Success' && strtotime($row['waktu_keberangkatan']) < time()) {
            $perjalanan_selesai++;
        }
    }
    // Reset pointer untuk digunakan lagi nanti
    $riwayat_pemesanan->execute(); // Re-execute query
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AgenBis</title>
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
                    <a href="../index.php" class="text-gray-600 hover:text-purple-600 transition">Beranda</a>
                    <a href="../jadwal.php" class="text-gray-600 hover:text-purple-600 transition">Jadwal</a>

                    <!-- User Menu -->
                    <div class="relative" id="user-menu">
                        <button
                            class="flex items-center space-x-2 text-gray-700 hover:text-purple-600 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg transition"
                            id="user-menu-button">
                            <img class="w-8 h-8 rounded-full"
                                src="<?= $user_data['foto_profil'] ? '../assets/images/'.$user_data['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($user_data['nama']).'&background=purple&color=white' ?>"
                                alt="<?= $user_data['nama'] ?>">
                            <span class="font-medium"><?= $user_data['nama'] ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform" id="chevron-icon"></i>
                        </button>

                        <div class="absolute right-0 w-48 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all duration-200 transform -translate-y-2 z-50"
                            id="user-dropdown">
                            <div class="py-1">
                                <a href="profile.php"
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition">
                                    <i class="fas fa-user mr-3"></i>
                                    Profil Saya
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="../logout.php"
                                    class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- HANYA SATU DASHBOARD HEADER -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, <?= $user_data['nama'] ?>! 👋</h1>
                    <p class="text-gray-600 mt-2">Kelola pemesanan tiket bus Anda dengan mudah</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Member sejak</p>
                    <p class="font-semibold"><?= date('d M Y', strtotime($user_data['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-ticket-alt text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Pemesanan</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_pemesanan ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tiket Aktif</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $tiket_aktif ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-history text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Perjalanan Selesai</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $perjalanan_selesai ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow-md mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Pemesanan Terbaru</h2>
            </div>
            <div class="p-6">
                <?php if($riwayat_pemesanan && $riwayat_pemesanan->rowCount() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Booking</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rute</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($row = $riwayat_pemesanan->fetch()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['kode_booking'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['kota_asal'] ?> →
                                        <?= $row['kota_tujuan'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $row['model'] ?> - <?= $row['nama_tipe'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d M Y', strtotime($row['waktu_keberangkatan'])) ?></div>
                                    <div class="text-sm text-gray-500">
                                        <?= date('H:i', strtotime($row['waktu_keberangkatan'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                        $status_class = [
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Success' => 'bg-green-100 text-green-800',
                                            'Failed' => 'bg-red-100 text-red-800',
                                            'Expired' => 'bg-gray-100 text-gray-800'
                                        ];
                                        ?>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class[$row['status_pembayaran']] ?>">
                                        <?= $row['status_pembayaran'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="pemesanan_detail.php?id=<?= $row['id'] ?>"
                                        class="text-purple-600 hover:text-purple-900 mr-3">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <?php if($row['status_pembayaran'] == 'Pending'): ?>
                                    <a href="bayar.php?id=<?= $row['id'] ?>"
                                        class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-credit-card"></i> Bayar
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada pemesanan</p>
                    <a href="../jadwal.php"
                        class="inline-block mt-4 bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        Pesan Tiket Sekarang
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions - TANPA SIDEBAR DUPLIKAT -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Pesan Tiket Baru</h3>
                <p class="text-gray-600 mb-4">Cari dan pesan tiket bus untuk perjalanan Anda</p>
                <a href="../jadwal.php"
                    class="inline-flex items-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search mr-2"></i> Cari Jadwal
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Kelola Profil</h3>
                <p class="text-gray-600 mb-4">Perbarui informasi profil dan preferensi Anda</p>
                <a href="profile.php"
                    class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-user-edit mr-2"></i> Edit Profil
                </a>
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
    // Dropdown User Menu JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        const chevronIcon = document.getElementById('chevron-icon');

        let isOpen = false;

        // Toggle dropdown
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;

            if (isOpen) {
                userDropdown.classList.remove('opacity-0', 'invisible', '-translate-y-2');
                userDropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
                chevronIcon.classList.add('rotate-180');
            } else {
                userDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                userDropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                chevronIcon.classList.remove('rotate-180');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                userDropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                chevronIcon.classList.remove('rotate-180');
                isOpen = false;
            }
        });

        // Konfirmasi logout
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