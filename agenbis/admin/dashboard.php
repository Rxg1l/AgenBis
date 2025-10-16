<?php
require_once "../config/Database.php";
require_once "../models/Bus.php";
require_once "../models/Jadwal.php";
require_once "../models/Pemesanan.php";
require_once "../models/Rute.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$bus = new Bus($db);
$jadwal = new Jadwal($db);
$pemesanan = new Pemesanan($db);
$rute = new Rute($db);

// Stats
$total_bus = $bus->readAll()->rowCount();
$total_jadwal = $jadwal->readAll()->rowCount();
$total_pemesanan = $pemesanan->readAll()->rowCount();
$total_rute = $rute->readAll()->rowCount();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AgenBis</title>
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
                    <span class="text-xl font-bold text-gray-800">AgenBis Admin</span>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-600 hover:text-purple-600">Beranda</a>
                    <a href="../jadwal.php" class="text-gray-600 hover:text-purple-600">Jadwal</a>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-purple-600">
                            <img class="w-8 h-8 rounded-full"
                                src="https://ui-avatars.com/api/?name=Admin&background=purple&color=white" alt="Admin">
                            <span>Admin</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div
                            class="absolute right-0 w-48 mt-2 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
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

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-gray-600 mt-2">Kelola sistem AgenBis dengan mudah</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-bus text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Bus</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_bus ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Jadwal</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_jadwal ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Pemesanan</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_pemesanan ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full mr-4">
                        <i class="fas fa-route text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Rute</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_rute ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <a href="bus.php"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-bus text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Kelola Bus</h3>
                        <p class="text-gray-600 text-sm">Tambah, edit, hapus data bus</p>
                    </div>
                </div>
            </a>

            <a href="jadwal.php"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Kelola Jadwal</h3>
                        <p class="text-gray-600 text-sm">Atur jadwal keberangkatan</p>
                    </div>
                </div>
            </a>

            <a href="rute.php"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-route text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Kelola Rute</h3>
                        <p class="text-gray-600 text-sm">Kelola rute perjalanan</p>
                    </div>
                </div>
            </a>

            <a href="pemesanan.php"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full mr-4">
                        <i class="fas fa-ticket-alt text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pemesanan</h3>
                        <p class="text-gray-600 text-sm">Lihat semua pemesanan</p>
                    </div>
                </div>
            </a>

            <a href="laporan.php"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <i class="fas fa-chart-bar text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Laporan</h3>
                        <p class="text-gray-600 text-sm">Statistik dan analisis</p>
                    </div>
                </div>
            </a>

            <a href="users.php"
                class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 border-l-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pengguna</h3>
                        <p class="text-gray-600 text-sm">Kelola data pengguna</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Pemesanan Terbaru</h2>
                </div>
                <div class="p-6">
                    <?php
                    $recent_bookings = $pemesanan->readRecent(5);
                    if($recent_bookings->rowCount() > 0):
                    ?>
                    <div class="space-y-4">
                        <?php while($booking = $recent_bookings->fetch()): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-800"><?= $booking['kode_booking'] ?></p>
                                <p class="text-sm text-gray-600"><?= $booking['kota_asal'] ?> →
                                    <?= $booking['kota_tujuan'] ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-purple-600">Rp
                                    <?= number_format($booking['total_harga'], 0, ',', '.') ?></p>
                                <span class="text-xs px-2 py-1 rounded-full <?= 
                                        $booking['status_pembayaran'] == 'Success' ? 'bg-green-100 text-green-800' : 
                                        ($booking['status_pembayaran'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                    ?>">
                                    <?= $booking['status_pembayaran'] ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-500 text-center py-4">Belum ada pemesanan</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bus Status -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Status Bus</h2>
                </div>
                <div class="p-6">
                    <?php
                    $all_bus = $bus->readAll();
                    if($all_bus->rowCount() > 0):
                    ?>
                    <div class="space-y-3">
                        <?php while($bus_data = $all_bus->fetch()): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full <?= 
                                        $bus_data['status_parkir'] == 'Tersedia' ? 'bg-green-500' : 
                                        ($bus_data['status_parkir'] == 'Perjalanan' ? 'bg-yellow-500' : 'bg-red-500')
                                    ?> mr-3"></div>
                                <div>
                                    <p class="font-semibold text-gray-800"><?= $bus_data['plat_nomor'] ?></p>
                                    <p class="text-sm text-gray-600"><?= $bus_data['model'] ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Energi</p>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full"
                                        style="width: <?= $bus_data['level_energi'] ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-500"><?= $bus_data['level_energi'] ?>%</p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-500 text-center py-4">Tidak ada data bus</p>
                    <?php endif; ?>
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