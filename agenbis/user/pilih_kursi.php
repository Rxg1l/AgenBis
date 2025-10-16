<?php
require_once "../config/Database.php";
require_once "../models/Jadwal.php";
require_once "../models/DenahKursi.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAuth();

if($auth->isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit;
}

$jadwal = new Jadwal($db);
$denah_kursi = new DenahKursi($db);

// Get schedule details
if(!isset($_GET['id'])) {
    header("Location: ../jadwal.php");
    exit;
}

$jadwal->id = $_GET['id'];
$jadwal_data = $jadwal->readOne();

if(!$jadwal_data) {
    header("Location: ../jadwal.php");
    exit;
}

// Get available seats
$kursi_tersedia = $denah_kursi->getKursiTersedia($jadwal_data['id']);
$kursi_array = [];
while($kursi = $kursi_tersedia->fetch()) {
    $kursi_array[] = $kursi;
}

// Handle seat selection
$selected_seats = [];
$error = '';

if($_POST && isset($_POST['lanjut_pembayaran'])) {
    $selected_seats = $_POST['kursi'] ?? [];
    $jumlah_tiket = count($selected_seats);
    
    if($jumlah_tiket == 0) {
        $error = "Pilih minimal 1 kursi";
    } elseif($jumlah_tiket > 5) {
        $error = "Maksimal 5 kursi per pemesanan";
    } else {
        // Simpan kursi yang dipilih di session untuk proses selanjutnya
        $_SESSION['selected_seats'] = $selected_seats;
        $_SESSION['jadwal_id'] = $jadwal_data['id'];
        $_SESSION['jumlah_tiket'] = $jumlah_tiket;
        $_SESSION['total_harga'] = $jadwal_data['harga'] * $jumlah_tiket;
        
        header("Location: isi_data_penumpang.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Kursi - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .bus-layout {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        max-width: 300px;
        margin: 0 auto;
    }

    .seat {
        width: 40px;
        height: 40px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .seat.available {
        background-color: #10b981;
        color: white;
        border-color: #10b981;
    }

    .seat.available:hover {
        background-color: #059669;
        transform: scale(1.1);
    }

    .seat.selected {
        background-color: #8b5cf6;
        color: white;
        border-color: #8b5cf6;
        transform: scale(1.1);
    }

    .seat.premium {
        background-color: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }

    .seat.disabled {
        background-color: #ef4444;
        color: white;
        border-color: #ef4444;
        cursor: not-allowed;
    }

    .seat.unavailable {
        background-color: #9ca3af;
        color: white;
        border-color: #9ca3af;
        cursor: not-allowed;
    }

    .driver-area {
        grid-column: 1 / -1;
        text-align: center;
        padding: 10px;
        background-color: #374151;
        color: white;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .aisle {
        grid-column: 2 / 4;
        height: 20px;
        background-color: #f3f4f6;
        border-radius: 4px;
        margin: 10px 0;
    }
    </style>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Pilih Kursi</h1>
            <p class="text-gray-600">Pilih kursi untuk perjalanan
                <?= htmlspecialchars($jadwal_data['kota_asal'] ?? '') ?> →
                <?= htmlspecialchars($jadwal_data['kota_tujuan'] ?? '') ?></p>
        </div>

        <?php if($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Bus Layout -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Denah Kursi Bus</h2>

                    <form method="POST" id="seatForm">
                        <div class="bus-layout">
                            <!-- Area Supir -->
                            <div class="driver-area">
                                <i class="fas fa-steering-wheel text-xl"></i>
                                <div class="text-xs mt-1">SUPIR</div>
                            </div>

                            <!-- Baris Kursi -->
                            <?php
                            $current_row = 0;
                            foreach($kursi_array as $kursi):
                                if($kursi['baris'] != $current_row):
                                    if($current_row > 0):
                                        // Tambah aisle setelah baris 2
                                        if($current_row == 2):
                            ?>
                            <div class="aisle">AISLE</div>
                            <?php
                                        endif;
                                    endif;
                                    $current_row = $kursi['baris'];
                                endif;
                            ?>
                            <div class="seat available <?= $kursi['tipe_kursi'] == 'Premium' ? 'premium' : '' ?>"
                                data-seat="<?= $kursi['nomor_kursi'] ?>" data-type="<?= $kursi['tipe_kursi'] ?>">
                                <?= $kursi['nomor_kursi'] ?>
                                <input type="checkbox" name="kursi[]" value="<?= $kursi['nomor_kursi'] ?>"
                                    class="hidden" id="seat-<?= $kursi['nomor_kursi'] ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Legend -->
                        <div class="mt-8 flex justify-center space-x-6 text-sm">
                            <div class="flex items-center">
                                <div class="seat available w-4 h-4 mr-2"></div>
                                <span>Reguler</span>
                            </div>
                            <div class="flex items-center">
                                <div class="seat premium w-4 h-4 mr-2"></div>
                                <span>Premium</span>
                            </div>
                            <div class="flex items-center">
                                <div class="seat selected w-4 h-4 mr-2"></div>
                                <span>Dipilih</span>
                            </div>
                            <div class="flex items-center">
                                <div class="seat unavailable w-4 h-4 mr-2"></div>
                                <span>Tidak Tersedia</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pemesanan</h2>

                <div class="space-y-4">
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($jadwal_data['kota_asal'] ?? '') ?>
                            → <?= htmlspecialchars($jadwal_data['kota_tujuan'] ?? '') ?></h3>
                        <p class="text-sm text-gray-600">
                            <?= date('d M Y', strtotime($jadwal_data['waktu_keberangkatan'] ?? '')) ?> -
                            <?= date('H:i', strtotime($jadwal_data['waktu_keberangkatan'] ?? '')) ?></p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Harga per kursi</span>
                            <span class="font-semibold">Rp
                                <?= number_format($jadwal_data['harga'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Jumlah kursi</span>
                            <span id="selectedCount" class="font-semibold">0</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800">Total</span>
                            <span id="totalPrice" class="text-lg font-bold text-purple-600">Rp 0</span>
                        </div>
                    </div>

                    <div id="selectedSeats" class="bg-yellow-50 p-4 rounded-lg hidden">
                        <h4 class="font-semibold text-gray-800 mb-2">Kursi Dipilih:</h4>
                        <div id="seatsList" class="text-sm text-gray-600"></div>
                    </div>

                    <button type="submit" form="seatForm" name="lanjut_pembayaran"
                        class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition font-bold disabled:bg-gray-400 disabled:cursor-not-allowed"
                        id="continueBtn" disabled>
                        <i class="fas fa-arrow-right mr-2"></i>Lanjutkan
                    </button>

                    <p class="text-xs text-gray-500 text-center">
                        * Maksimal 5 kursi per pemesanan<br>
                        * Kursi yang sudah dipilih tidak dapat diubah
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const seats = document.querySelectorAll('.seat.available');
        const selectedCount = document.getElementById('selectedCount');
        const totalPrice = document.getElementById('totalPrice');
        const selectedSeats = document.getElementById('selectedSeats');
        const seatsList = document.getElementById('seatsList');
        const continueBtn = document.getElementById('continueBtn');
        const pricePerSeat = <?= $jadwal_data['harga'] ?? 0 ?>;

        let selectedSeatsArray = [];

        seats.forEach(seat => {
            seat.addEventListener('click', function() {
                const seatNumber = this.getAttribute('data-seat');
                const seatType = this.getAttribute('data-type');
                const checkbox = document.getElementById(`seat-${seatNumber}`);

                if (checkbox.checked) {
                    // Unselect seat
                    checkbox.checked = false;
                    this.classList.remove('selected');
                    selectedSeatsArray = selectedSeatsArray.filter(s => s !== seatNumber);
                } else {
                    // Select seat (max 5)
                    if (selectedSeatsArray.length >= 5) {
                        alert('Maksimal 5 kursi per pemesanan');
                        return;
                    }
                    checkbox.checked = true;
                    this.classList.add('selected');
                    selectedSeatsArray.push(seatNumber);
                }

                updateSummary();
            });
        });

        function updateSummary() {
            const count = selectedSeatsArray.length;
            const total = pricePerSeat * count;

            selectedCount.textContent = count;
            totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');

            if (count > 0) {
                seatsList.innerHTML = selectedSeatsArray.join(', ');
                selectedSeats.classList.remove('hidden');
                continueBtn.disabled = false;
            } else {
                selectedSeats.classList.add('hidden');
                continueBtn.disabled = true;
            }
        }

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