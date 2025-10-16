<?php
require_once "../config/Database.php";
require_once "../models/Pemesanan.php";
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

// Check session data
if(!isset($_SESSION['selected_seats']) || !isset($_SESSION['penumpang_data']) || !isset($_SESSION['jadwal_id'])) {
    header("Location: ../jadwal.php");
    exit;
}

$selected_seats = $_SESSION['selected_seats'];
$penumpang_data = $_SESSION['penumpang_data'];
$jadwal_id = $_SESSION['jadwal_id'];
$jumlah_tiket = $_SESSION['jumlah_tiket'];
$total_harga = $_SESSION['total_harga'];

$pemesanan = new Pemesanan($db);
$jadwal = new Jadwal($db);

$jadwal->id = $jadwal_id;
$jadwal_data = $jadwal->readOne();

// Create booking
$pemesanan->user_id = $_SESSION['user_id'];
$pemesanan->jadwal_id = $jadwal_id;
$pemesanan->jumlah_tiket = $jumlah_tiket;
$pemesanan->total_harga = $total_harga;

$pemesanan_id = $pemesanan->createWithKursi($penumpang_data);

if($pemesanan_id) {
    // Clear session data
    unset($_SESSION['selected_seats']);
    unset($_SESSION['penumpang_data']);
    unset($_SESSION['jadwal_id']);
    unset($_SESSION['jumlah_tiket']);
    unset($_SESSION['total_harga']);
    
    header("Location: bayar.php?id=" . $pemesanan_id);
    exit;
} else {
    $error = "Gagal membuat pemesanan. Silakan coba lagi.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pemesanan - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full text-center">
            <?php if(isset($error)): ?>
            <div class="text-red-600 mb-4">
                <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                <h2 class="text-xl font-bold">Pemesanan Gagal</h2>
                <p class="mt-2"><?= $error ?></p>
            </div>
            <a href="../jadwal.php"
                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                Coba Lagi
            </a>
            <?php else: ?>
            <div class="text-green-600 mb-4">
                <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
                <h2 class="text-xl font-bold">Memproses Pemesanan...</h2>
                <p class="mt-2">Sedang membuat pemesanan Anda</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>