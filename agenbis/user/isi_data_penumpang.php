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

// Check if seats are selected
if(!isset($_SESSION['selected_seats']) || !isset($_SESSION['jadwal_id'])) {
    header("Location: ../jadwal.php");
    exit;
}

$selected_seats = $_SESSION['selected_seats'];
$jadwal_id = $_SESSION['jadwal_id'];
$jumlah_tiket = $_SESSION['jumlah_tiket'];
$total_harga = $_SESSION['total_harga'];

$jadwal = new Jadwal($db);
$jadwal->id = $jadwal_id;
$jadwal_data = $jadwal->readOne();

if(!$jadwal_data) {
    header("Location: ../jadwal.php");
    exit;
}

// Handle form submission
if($_POST && isset($_POST['buat_pemesanan'])) {
    $penumpang_data = [];
    $valid = true;
    
    for($i = 0; $i < $jumlah_tiket; $i++) {
        $nama = $_POST['nama_penumpang'][$i] ?? '';
        $no_identitas = $_POST['no_identitas'][$i] ?? '';
        
        if(empty($nama) || empty($no_identitas)) {
            $valid = false;
            $error = "Harap isi semua data penumpang";
            break;
        }
        
        $penumpang_data[] = [
            'nomor_kursi' => $selected_seats[$i],
            'nama_penumpang' => $nama,
            'no_identitas' => $no_identitas,
            'harga' => $jadwal_data['harga']
        ];
    }
    
    if($valid) {
        // Simpan data penumpang di session
        $_SESSION['penumpang_data'] = $penumpang_data;
        header("Location: buat_pemesanan.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penumpang - AgenBis</title>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Data Penumpang</h1>
            <p class="text-gray-600">Isi data penumpang untuk kursi yang dipilih</p>
        </div>

        <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Passenger Forms -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Form Data Penumpang</h2>

                    <form method="POST">
                        <?php for($i = 0; $i < $jumlah_tiket; $i++): ?>
                        <div class="border border-gray-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Penumpang <?= $i + 1 ?> - Kursi
                                <?= $selected_seats[$i] ?></h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama_penumpang[]" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                        placeholder="Nama lengkap penumpang">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Identitas
                                        (KTP/SIM)</label>
                                    <input type="text" name="no_identitas[]" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                        placeholder="Nomor KTP atau SIM">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>

                        <div class="flex justify-between items-center mt-6">
                            <a href="pilih_kursi.php?id=<?= $jadwal_id ?>"
                                class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                            <button type="submit" name="buat_pemesanan"
                                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-bold">
                                <i class="fas fa-check mr-2"></i>Buat Pemesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan</h2>

                <div class="space-y-4">
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($jadwal_data['kota_asal'] ?? '') ?>
                            → <?= htmlspecialchars($jadwal_data['kota_tujuan'] ?? '') ?></h3>
                        <p class="text-sm text-gray-600">
                            <?= date('d M Y', strtotime($jadwal_data['waktu_keberangkatan'] ?? '')) ?></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2">Kursi Dipilih:</h4>
                        <p class="text-sm text-gray-600"><?= implode(', ', $selected_seats) ?></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Jumlah penumpang</span>
                            <span class="font-semibold"><?= $jumlah_tiket ?> orang</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total pembayaran</span>
                            <span class="text-lg font-bold text-purple-600">Rp
                                <?= number_format($total_harga, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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