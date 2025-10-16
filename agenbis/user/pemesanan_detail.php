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

// Generate simple QR code (text-based for demo)
function generateSimpleQR($text) {
    $qr = "╔════════════════╗\n";
    $qr .= "║ 🚌 AGENBIS TICKET ║\n";
    $qr .= "║ " . str_pad(substr($text, 0, 16), 16) . " ║\n";
    $qr .= "║ " . str_pad(substr($text, 16, 16), 16) . " ║\n";
    $qr .= "║ " . date('Y-m-d H:i') . " ║\n";
    $qr .= "╚════════════════╝";
    return $qr;
}

$qr_code = generateSimpleQR($pemesanan_data['kode_booking']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .ticket {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .ticket::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20%;
        right: 20%;
        height: 2px;
        background: repeating-linear-gradient(90deg,
                transparent,
                transparent 10px,
                white 10px,
                white 20px);
    }

    .print-area {
        background: white;
    }

    @media print {
        .no-print {
            display: none;
        }

        .print-area {
            box-shadow: none;
            margin: 0;
            padding: 0;
        }
    }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg no-print">
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
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 no-print">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Detail Tiket</h1>
                    <p class="text-gray-600">Kode Booking:
                        <strong><?= htmlspecialchars($pemesanan_data['kode_booking'] ?? '') ?></strong>
                    </p>
                </div>
                <div class="space-x-2">
                    <button onclick="window.print()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-print mr-2"></i>Cetak Tiket
                    </button>
                    <a href="dashboard.php"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Ticket -->
        <div class="print-area bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Ticket Header -->
            <div class="ticket text-white p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">E-TICKET</h1>
                        <p class="text-purple-200">AgenBis Travel</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold"><?= htmlspecialchars($pemesanan_data['kode_booking'] ?? '') ?></p>
                        <p class="text-purple-200">Kode Booking</p>
                    </div>
                </div>
            </div>

            <!-- Ticket Body -->
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Journey Info -->
                    <div class="lg:col-span-2">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Detail Perjalanan</h2>

                            <div class="flex items-center justify-between mb-6">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Keberangkatan</p>
                                    <p class="text-xl font-bold text-gray-800">
                                        <?= htmlspecialchars($pemesanan_data['kota_asal'] ?? '') ?></p>
                                    <p class="text-lg text-gray-600">
                                        <?= date('H:i', strtotime($pemesanan_data['waktu_keberangkatan'] ?? '')) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?= date('d M Y', strtotime($pemesanan_data['waktu_keberangkatan'] ?? '')) ?>
                                    </p>
                                </div>

                                <div class="flex-1 mx-4">
                                    <div class="flex items-center justify-center">
                                        <div class="w-2 h-2 bg-purple-600 rounded-full"></div>
                                        <div class="flex-1 h-1 bg-purple-300"></div>
                                        <i class="fas fa-bus text-purple-600 mx-2"></i>
                                        <div class="flex-1 h-1 bg-purple-300"></div>
                                        <div class="w-2 h-2 bg-purple-600 rounded-full"></div>
                                    </div>
                                    <p class="text-center text-sm text-gray-600 mt-2">
                                        <?= $pemesanan_data['jarak_km'] ?? 0 ?> km •
                                        <?= $pemesanan_data['waktu_tempuh_jam'] ?? 0 ?> jam
                                    </p>
                                </div>

                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Tujuan</p>
                                    <p class="text-xl font-bold text-gray-800">
                                        <?= htmlspecialchars($pemesanan_data['kota_tujuan'] ?? '') ?></p>
                                    <p class="text-lg text-gray-600">
                                        <?= date('H:i', strtotime($pemesanan_data['waktu_tiba'] ?? '')) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?= date('d M Y', strtotime($pemesanan_data['waktu_tiba'] ?? '')) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Bus</p>
                                <p class="font-semibold"><?= htmlspecialchars($pemesanan_data['model'] ?? '') ?></p>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($pemesanan_data['plat_nomor'] ?? '') ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Tipe</p>
                                <p class="font-semibold"><?= htmlspecialchars($pemesanan_data['nama_tipe'] ?? '') ?></p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Jumlah Penumpang</p>
                                <p class="font-semibold"><?= $pemesanan_data['jumlah_tiket'] ?? 0 ?> orang</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total Pembayaran</p>
                                <p class="font-semibold text-purple-600">Rp
                                    <?= number_format($pemesanan_data['total_harga'] ?? 0, 0, ',', '.') ?></p>
                            </div>
                        </div>

                        <!-- Passenger Info -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Penumpang</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-semibold"><?= htmlspecialchars($pemesanan_data['nama_user'] ?? '') ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($pemesanan_data['email'] ?? '') ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($pemesanan_data['telepon'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code & Status -->
                    <div class="border-l border-gray-200 pl-8">
                        <div class="text-center">
                            <div class="bg-gray-100 p-4 rounded-lg mb-4">
                                <pre class="text-xs font-mono bg-white p-2 rounded"><?= $qr_code ?></pre>
                                <p class="text-sm text-gray-600 mt-2">Scan QR Code untuk check-in</p>
                            </div>

                            <div class="mb-6">
                                <?php 
                                $status_class = [
                                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'Success' => 'bg-green-100 text-green-800 border-green-300',
                                    'Failed' => 'bg-red-100 text-red-800 border-red-300',
                                    'Expired' => 'bg-gray-100 text-gray-800 border-gray-300'
                                ];
                                $status = $pemesanan_data['status_pembayaran'] ?? 'Pending';
                                ?>
                                <span
                                    class="px-4 py-2 rounded-full border-2 <?= $status_class[$status] ?> font-semibold">
                                    <?= $status ?>
                                </span>
                            </div>

                            <div class="space-y-3 text-left">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tanggal Pemesanan:</span>
                                    <span
                                        class="font-semibold"><?= date('d M Y H:i', strtotime($pemesanan_data['waktu_pemesanan'] ?? '')) ?></span>
                                </div>

                                <?php if(isset($pemesanan_data['metode_pembayaran']) && $pemesanan_data['metode_pembayaran']): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Metode Pembayaran:</span>
                                    <span
                                        class="font-semibold"><?= htmlspecialchars($pemesanan_data['metode_pembayaran']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-bold text-yellow-800 mb-2">Catatan Penting:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Harap hadir di terminal minimal 30 menit sebelum keberangkatan</li>
                        <li>• Bawa bukti identitas asli untuk check-in</li>
                        <li>• Tiket ini berlaku untuk <?= $pemesanan_data['jumlah_tiket'] ?? 0 ?> penumpang</li>
                        <li>• Untuk bantuan, hubungi customer service di 1500-123</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4 no-print">
            <?php if(($pemesanan_data['status_pembayaran'] ?? '') == 'Pending'): ?>
            <a href="bayar.php?id=<?= $pemesanan_data['id'] ?>"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-bold">
                <i class="fas fa-credit-card mr-2"></i>Bayar Sekarang
            </a>
            <?php endif; ?>

            <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                <i class="fas fa-print mr-2"></i>Cetak Tiket
            </button>

            <a href="dashboard.php"
                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-bold">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12 no-print">
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