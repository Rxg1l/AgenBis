<?php
require_once "../config/Database.php";
require_once "../models/Pemesanan.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAuth();

if($auth->isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit;
}

$pemesanan = new Pemesanan($db);

if(!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$pemesanan->id = $_GET['id'];
$pemesanan_data = $pemesanan->readOne();

if(!$pemesanan_data || $pemesanan_data['user_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit;
}

// Handle payment
if($_POST && isset($_POST['bayar'])) {
    $pemesanan->id = $pemesanan_data['id'];
    $pemesanan->status_pembayaran = 'Success';
    $pemesanan->metode_pembayaran = $_POST['metode_pembayaran'];
    
    if($pemesanan->updateStatus()) {
        $success = "Pembayaran berhasil! Tiket Anda telah dikonfirmasi.";
    } else {
        $error = "Gagal memproses pembayaran";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - AgenBis</title>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Tiket</h1>
            <p class="text-gray-600">Selesaikan pembayaran untuk mengkonfirmasi pemesanan Anda</p>
        </div>

        <?php if(isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $success ?>
            <div class="mt-2">
                <a href="pemesanan_detail.php?id=<?= $pemesanan_data['id'] ?>"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Lihat Detail Tiket
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <?php if($pemesanan_data['status_pembayaran'] != 'Success'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pemesanan</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800"><?= $pemesanan_data['kota_asal'] ?> →
                                    <?= $pemesanan_data['kota_tujuan'] ?></h3>
                                <p class="text-gray-600">Kode Booking: <?= $pemesanan_data['kode_booking'] ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-600">Rp
                                    <?= number_format($pemesanan_data['total_harga'], 0, ',', '.') ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Tanggal Keberangkatan</p>
                                <p class="font-semibold">
                                    <?= date('d M Y', strtotime($pemesanan_data['waktu_keberangkatan'])) ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Waktu</p>
                                <p class="font-semibold">
                                    <?= date('H:i', strtotime($pemesanan_data['waktu_keberangkatan'])) ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Bus</p>
                                <p class="font-semibold"><?= $pemesanan_data['model'] ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Tipe</p>
                                <p class="font-semibold"><?= $pemesanan_data['nama_tipe'] ?></p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-yellow-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-yellow-800">Batas Waktu Pembayaran</p>
                                    <p class="text-yellow-700">
                                        <?= date('d M Y H:i', strtotime($pemesanan_data['waktu_kadaluarsa'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Metode Pembayaran</h2>

                <form method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Metode</label>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="metode_pembayaran" value="Transfer Bank"
                                        class="text-purple-600" checked>
                                    <span class="ml-3">
                                        <span class="block font-medium">Transfer Bank</span>
                                        <span class="block text-sm text-gray-600">BNI, BCA, Mandiri, BRI</span>
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="metode_pembayaran" value="E-Wallet"
                                        class="text-purple-600">
                                    <span class="ml-3">
                                        <span class="block font-medium">E-Wallet</span>
                                        <span class="block text-sm text-gray-600">Gopay, OVO, Dana</span>
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="metode_pembayaran" value="Kartu Kredit"
                                        class="text-purple-600">
                                    <span class="ml-3">
                                        <span class="block font-medium">Kartu Kredit</span>
                                        <span class="block text-sm text-gray-600">Visa, Mastercard</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Total Pembayaran</span>
                                <span class="text-xl font-bold text-purple-600">Rp
                                    <?= number_format($pemesanan_data['total_harga'], 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <button type="submit" name="bayar"
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition font-bold">
                            <i class="fas fa-check-circle mr-2"></i>Konfirmasi Pembayaran
                        </button>

                        <p class="text-xs text-gray-500 text-center">
                            Dengan mengklik tombol di atas, Anda menyetujui syarat dan ketentuan yang berlaku
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h2>
            <p class="text-gray-600 mb-6">Tiket Anda telah berhasil dipesan dan dikonfirmasi</p>
            <div class="space-x-4">
                <a href="pemesanan_detail.php?id=<?= $pemesanan_data['id'] ?>"
                    class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                    Lihat Detail Tiket
                </a>
                <a href="dashboard.php"
                    class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

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

</body>

</html>