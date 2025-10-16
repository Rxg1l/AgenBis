<?php
require_once "../config/Database.php";
require_once "../models/Pemesanan.php";
require_once "../models/Bus.php";
require_once "../models/Jadwal.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$pemesanan = new Pemesanan($db);
$bus = new Bus($db);
$jadwal = new Jadwal($db);

// Get date range
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get statistics
$total_pendapatan = $pemesanan->getTotalRevenue($start_date, $end_date);
$total_penumpang = $pemesanan->getTotalPassengers($start_date, $end_date);
$rata_rata_penumpang = $pemesanan->getAveragePassengers($start_date, $end_date);
$bus_terpopuler = $bus->getMostPopularBus($start_date, $end_date);
$rute_terpopuler = $jadwal->getMostPopularRoute($start_date, $end_date);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="dashboard.php" class="text-gray-600 hover:text-purple-600">Dashboard</a>
                    <a href="bus.php" class="text-gray-600 hover:text-purple-600">Bus</a>
                    <a href="jadwal.php" class="text-gray-600 hover:text-purple-600">Jadwal</a>
                    <a href="laporan.php" class="text-purple-600 font-medium">Laporan</a>
                    <a href="../logout.php"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan & Analitik</h1>
            <p class="text-gray-600">Analisis kinerja dan statistik sistem</p>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Filter Periode</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?= $start_date ?>"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?= $end_date ?>"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-800">Rp
                            <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Penumpang</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($total_penumpang, 0, ',', '.') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Rata-rata Penumpang/Hari</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($rata_rata_penumpang, 1) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full mr-4">
                        <i class="fas fa-bus text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Bus Terpopuler</p>
                        <p class="text-lg font-bold text-gray-800"><?= $bus_terpopuler['model'] ?? '-' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Pendapatan Bulanan</h2>
                <canvas id="revenueChart" height="300"></canvas>
            </div>

            <!-- Passenger Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Distribusi Penumpang</h2>
                <canvas id="passengerChart" height="300"></canvas>
            </div>
        </div>

        <!-- Popular Routes -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Rute Terpopuler</h2>
            </div>
            <div class="p-6">
                <?php if($rute_terpopuler): ?>
                <div class="space-y-4">
                    <?php foreach($rute_terpopuler as $rute): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-2 rounded-full mr-4">
                                <i class="fas fa-route text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800"><?= $rute['kota_asal'] ?> →
                                    <?= $rute['kota_tujuan'] ?></p>
                                <p class="text-sm text-gray-600"><?= $rute['jumlah_penumpang'] ?> penumpang</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-purple-600">Rp
                                <?= number_format($rute['total_pendapatan'], 0, ',', '.') ?></p>
                            <p class="text-sm text-gray-600">Pendapatan</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-center py-4">Tidak ada data rute</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: [25000000, 32000000, 28000000, 35000000, 42000000, 38000000, 45000000, 48000000,
                    42000000, 50000000, 55000000, 60000000
                ],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Passenger Chart
    const passengerCtx = document.getElementById('passengerChart').getContext('2d');
    const passengerChart = new Chart(passengerCtx, {
        type: 'doughnut',
        data: {
            labels: ['Ekspres', 'Kota', 'VIP', 'Ekonomi'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: [
                    '#8b5cf6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

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