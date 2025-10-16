<?php
require_once "../config/Database.php";
require_once "../models/Jadwal.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAuth();

if($auth->isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Redirect langsung ke halaman pilih kursi
if(!isset($_GET['id'])) {
    header("Location: ../jadwal.php");
    exit;
}

header("Location: pilih_kursi.php?id=" . $_GET['id']);
exit;
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tiket - AgenBis</title>
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
                    <a href="../index.php" class="text-gray-600 hover:text-purple-600">Beranda</a>
                    <a href="../jadwal.php" class="text-gray-600 hover:text-purple-600">Jadwal</a>
                    <a href="dashboard.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pesan Tiket Bus</h1>
            <p class="text-gray-600">Lengkapi informasi pemesanan Anda</p>
        </div>

        <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Schedule Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Perjalanan</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">
                                    <?= htmlspecialchars($jadwal_data['kota_asal'] ?? '') ?> →
                                    <?= htmlspecialchars($jadwal_data['kota_tujuan'] ?? '') ?></h3>
                                <p class="text-gray-600"><?= htmlspecialchars($jadwal_data['nama_tipe'] ?? '') ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-600">Rp
                                    <?= number_format($jadwal_data['harga'] ?? 0, 0, ',', '.') ?></p>
                                <p class="text-sm text-gray-600">per orang</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Tanggal Keberangkatan</p>
                                <p class="font-semibold">
                                    <?= date('d M Y', strtotime($jadwal_data['waktu_keberangkatan'] ?? '')) ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Waktu</p>
                                <p class="font-semibold">
                                    <?= date('H:i', strtotime($jadwal_data['waktu_keberangkatan'] ?? '')) ?> -
                                    <?= date('H:i', strtotime($jadwal_data['waktu_tiba'] ?? '')) ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Bus</p>
                                <p class="font-semibold"><?= htmlspecialchars($jadwal_data['model'] ?? '') ?>
                                    (<?= htmlspecialchars($jadwal_data['plat_nomor'] ?? '') ?>)</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Kursi Tersedia</p>
                                <p class="font-semibold"><?= $jadwal_data['kursi_tersedia'] ?? 0 ?> kursi</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Fasilitas</p>
                            <p class="font-semibold"><?= htmlspecialchars($jadwal_data['fasilitas'] ?? '') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Form Pemesanan</h2>

                <form method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Tiket</label>
                            <select name="jumlah_tiket" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <?php 
                                $max_tickets = min(5, $jadwal_data['kursi_tersedia'] ?? 1);
                                for($i = 1; $i <= $max_tickets; $i++): 
                                ?>
                                <option value="<?= $i ?>"><?= $i ?> tiket</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-800 mb-2">Informasi Penting</h3>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Batas pembayaran: 2 jam setelah pemesanan</li>
                                <li>• Tiket tidak dapat di-refund</li>
                                <li>• Check-in minimal 30 menit sebelum keberangkatan</li>
                            </ul>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Harga per tiket</span>
                                <span class="font-semibold">Rp
                                    <?= number_format($jadwal_data['harga'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Jumlah tiket</span>
                                <span id="ticketCount" class="font-semibold">1</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">Total</span>
                                <span id="totalPrice" class="text-lg font-bold text-purple-600">
                                    Rp <?= number_format($jadwal_data['harga'] ?? 0, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>

                        <button type="submit" name="pesan"
                            class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition font-bold">
                            <i class="fas fa-credit-card mr-2"></i>Lanjut ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const hargaPerTiket = <?= $jadwal_data['harga'] ?? 0 ?>;
    const selectJumlah = document.querySelector('select[name="jumlah_tiket"]');
    const ticketCount = document.getElementById('ticketCount');
    const totalPrice = document.getElementById('totalPrice');

    function updateTotal() {
        const jumlah = parseInt(selectJumlah.value);
        ticketCount.textContent = jumlah;
        totalPrice.textContent = 'Rp ' + (hargaPerTiket * jumlah).toLocaleString('id-ID');
    }

    selectJumlah.addEventListener('change', updateTotal);
    updateTotal(); // Initial calculation
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

    const hargaPerTiket = <?= $jadwal_data['harga'] ?? 0 ?>;
    const selectJumlah = document.querySelector('select[name="jumlah_tiket"]');
    const ticketCount = document.getElementById('ticketCount');
    const totalPrice = document.getElementById('totalPrice');

    function updateTotal() {
        const jumlah = parseInt(selectJumlah.value);
        ticketCount.textContent = jumlah;
        totalPrice.textContent = 'Rp ' + (hargaPerTiket * jumlah).toLocaleString('id-ID');
    }

    selectJumlah.addEventListener('change', updateTotal);
    updateTotal(); // Initial calculation
    </script>
</body>

</html>