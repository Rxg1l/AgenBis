<?php
require_once "../config/Database.php";
require_once "../models/Jadwal.php";
require_once "../models/Bus.php";
require_once "../models/Rute.php";
require_once "../models/TipeTiket.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$jadwal = new Jadwal($db);
$bus = new Bus($db);
$rute = new Rute($db);
$tipe_tiket = new TipeTiket($db);

// Get data for dropdowns
$all_bus = $bus->readAll();
$all_rute = $rute->readAll();
$all_tipe = $tipe_tiket->readAll();

// Handle form actions
if($_POST) {
    if(isset($_POST['create'])) {
        $jadwal->bus_id = $_POST['bus_id'];
        $jadwal->rute_id = $_POST['rute_id'];
        $jadwal->tipe_tiket_id = $_POST['tipe_tiket_id'];
        $jadwal->waktu_keberangkatan = $_POST['waktu_keberangkatan'];
        $jadwal->waktu_tiba = $_POST['waktu_tiba'];
        $jadwal->harga = $_POST['harga'];
        $jadwal->kursi_tersedia = $_POST['kursi_tersedia'];
        $jadwal->status = $_POST['status'];
        
        if($jadwal->create()) {
            $success = "Jadwal berhasil ditambahkan";
        } else {
            $error = "Gagal menambahkan jadwal";
        }
    }

    // Handle form actions
if($_POST) {
    if(isset($_POST['create'])) {
        // Validasi harga
        $harga = $_POST['harga'];
        
        if($harga < 1000) {
            $error = "Harga minimal Rp 1.000";
        } elseif($harga > 10000000) {
            $error = "Harga maksimal Rp 10.000.000";
        } else {
            $jadwal->bus_id = $_POST['bus_id'];
            $jadwal->rute_id = $_POST['rute_id'];
            $jadwal->tipe_tiket_id = $_POST['tipe_tiket_id'];
            $jadwal->waktu_keberangkatan = $_POST['waktu_keberangkatan'];
            $jadwal->waktu_tiba = $_POST['waktu_tiba'];
            $jadwal->harga = $harga;
            $jadwal->kursi_tersedia = $_POST['kursi_tersedia'];
            $jadwal->status = $_POST['status'];
            
            if($jadwal->create()) {
                $success = "Jadwal berhasil ditambahkan";
            } else {
                $error = "Gagal menambahkan jadwal";
            }
        }
    }
    
    // ... kode update yang sudah ada
}

}

$all_jadwal = $jadwal->readAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal - AgenBis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<script>
// Format Rupiah untuk input harga
function formatRupiah(input) {
    // Hapus karakter selain angka
    let value = input.value.replace(/[^\d]/g, '');

    // Format dengan titik
    if (value.length > 0) {
        value = parseInt(value).toLocaleString('id-ID');
    }

    input.value = value;
}

// Konversi kembali ke angka sebelum submit
function convertToNumber() {
    const hargaInput = document.getElementById('harga');
    if (hargaInput) {
        let value = hargaInput.value.replace(/[^\d]/g, '');
        hargaInput.value = value;
    }
}

// Panggil convertToNumber sebelum form submit
document.getElementById('jadwalForm').addEventListener('submit', function(e) {
    convertToNumber();
});

// Atau gunakan input type number dengan range yang lebih besar
function setupHargaInput() {
    const hargaInput = document.getElementById('harga');
    if (hargaInput && hargaInput.type === 'number') {
        hargaInput.min = 1000;
        hargaInput.step = 1000;
        hargaInput.max = 10000000; // 10 juta
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupHargaInput();

    // ... kode dropdown dan lainnya yang sudah ada
});
</script>

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
                    <a href="jadwal.php" class="text-purple-600 font-medium">Jadwal</a>
                    <a href="rute.php" class="text-gray-600 hover:text-purple-600">Rute</a>
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
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Jadwal Keberangkatan</h1>
            <button onclick="openModal('create')"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-plus mr-2"></i>Tambah Jadwal
            </button>
        </div>

        <!-- Notifications -->
        <?php if(isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $success ?>
        </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <!-- Jadwal Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Daftar Jadwal</h2>
            </div>
            <div class="p-6">
                <?php if($all_jadwal->rowCount() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rute</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bus</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kursi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($row = $all_jadwal->fetch()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['kota_asal'] ?> →
                                        <?= $row['kota_tujuan'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $row['nama_tipe'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['model'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $row['plat_nomor'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d M Y', strtotime($row['waktu_keberangkatan'])) ?></div>
                                    <div class="text-sm text-gray-500">
                                        <?= date('H:i', strtotime($row['waktu_keberangkatan'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['kursi_tersedia'] ?> kursi</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                        $status_class = [
                                            'Aktif' => 'bg-green-100 text-green-800',
                                            'Nonaktif' => 'bg-red-100 text-red-800',
                                            'Penuh' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        ?>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class[$row['status']] ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editJadwal(<?= htmlspecialchars(json_encode($row)) ?>)"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada jadwal</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="jadwalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 mb-4">Tambah Jadwal Baru</h3>

                <form id="jadwalForm" method="POST">
                    <input type="hidden" id="jadwalId" name="id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bus</label>
                            <select id="bus_id" name="bus_id" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Bus</option>
                                <?php while($bus_row = $all_bus->fetch()): ?>
                                <option value="<?= $bus_row['id'] ?>">
                                    <?= $bus_row['plat_nomor'] ?> - <?= $bus_row['model'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rute</label>
                            <select id="rute_id" name="rute_id" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Rute</option>
                                <?php while($rute_row = $all_rute->fetch()): ?>
                                <option value="<?= $rute_row['id'] ?>">
                                    <?= $rute_row['kota_asal'] ?> → <?= $rute_row['kota_tujuan'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Tiket</label>
                            <select id="tipe_tiket_id" name="tipe_tiket_id" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Pilih Tipe</option>
                                <?php while($tipe_row = $all_tipe->fetch()): ?>
                                <option value="<?= $tipe_row['id'] ?>"><?= $tipe_row['nama_tipe'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                                <option value="Penuh">Penuh</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu Keberangkatan</label>
                            <input type="datetime-local" id="waktu_keberangkatan" name="waktu_keberangkatan" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu Tiba</label>
                            <input type="datetime-local" id="waktu_tiba" name="waktu_tiba" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                            <input type="number" id="harga" name="harga" required min="1000" step="1000"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Contoh: 150000">
                            <p class="text-xs text-gray-500 mt-1">Minimal Rp 1.000</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kursi Tersedia</label>
                            <input type="number" id="kursi_tersedia" name="kursi_tersedia" required min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn" name="create"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openModal(action) {
        const modal = document.getElementById('jadwalModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('jadwalForm');
        const submitBtn = document.getElementById('submitBtn');

        if (action === 'create') {
            title.textContent = 'Tambah Jadwal Baru';
            form.reset();
            submitBtn.name = 'create';
            submitBtn.textContent = 'Simpan';
            document.getElementById('jadwalId').value = '';
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('jadwalModal').classList.add('hidden');
    }

    function editJadwal(jadwal) {
        openModal('edit');
        document.getElementById('modalTitle').textContent = 'Edit Jadwal';
        document.getElementById('jadwalId').value = jadwal.id;
        document.getElementById('bus_id').value = jadwal.bus_id;
        document.getElementById('rute_id').value = jadwal.rute_id;
        document.getElementById('tipe_tiket_id').value = jadwal.tipe_tiket_id;
        document.getElementById('status').value = jadwal.status;
        document.getElementById('waktu_keberangkatan').value = jadwal.waktu_keberangkatan.replace(' ', 'T');
        document.getElementById('waktu_tiba').value = jadwal.waktu_tiba.replace(' ', 'T');
        document.getElementById('harga').value = jadwal.harga;
        document.getElementById('kursi_tersedia').value = jadwal.kursi_tersedia;

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.name = 'update';
        submitBtn.textContent = 'Update';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('jadwalModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Set min datetime to current
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('waktu_keberangkatan').min = now.toISOString().slice(0, 16);
    document.getElementById('waktu_tiba').min = now.toISOString().slice(0, 16);

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