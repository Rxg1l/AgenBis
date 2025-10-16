<?php
require_once "../config/Database.php";
require_once "../models/Bus.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$bus = new Bus($db);

// Handle form actions
if($_POST) {
    if(isset($_POST['create'])) {
        $bus->plat_nomor = $_POST['plat_nomor'];
        $bus->model = $_POST['model'];
        $bus->kapasitas = $_POST['kapasitas'];
        $bus->fasilitas = $_POST['fasilitas'];
        $bus->status_parkir = $_POST['status_parkir'];
        $bus->level_energi = $_POST['level_energi'];
        
        if($bus->create()) {
            $success = "Bus berhasil ditambahkan";
        } else {
            $error = "Gagal menambahkan bus";
        }
    }
    
    if(isset($_POST['update'])) {
        $bus->id = $_POST['id'];
        $bus->plat_nomor = $_POST['plat_nomor'];
        $bus->model = $_POST['model'];
        $bus->kapasitas = $_POST['kapasitas'];
        $bus->fasilitas = $_POST['fasilitas'];
        $bus->status_parkir = $_POST['status_parkir'];
        $bus->level_energi = $_POST['level_energi'];
        
        if($bus->update()) {
            $success = "Bus berhasil diupdate";
        } else {
            $error = "Gagal mengupdate bus";
        }
    }
}

if(isset($_GET['delete'])) {
    $bus->id = $_GET['delete'];
    if($bus->delete()) {
        $success = "Bus berhasil dihapus";
    } else {
        $error = "Gagal menghapus bus";
    }
}

$all_bus = $bus->readAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Bus - AgenBis</title>
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
                    <a href="dashboard.php" class="text-gray-600 hover:text-purple-600">Dashboard</a>
                    <a href="bus.php" class="text-purple-600 font-medium">Bus</a>
                    <a href="jadwal.php" class="text-gray-600 hover:text-purple-600">Jadwal</a>
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
            <h1 class="text-2xl font-bold text-gray-800">Kelola Data Bus</h1>
            <button onclick="openModal('create')"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-plus mr-2"></i>Tambah Bus
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

        <!-- Bus Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Daftar Bus</h2>
            </div>
            <div class="p-6">
                <?php if($all_bus->rowCount() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Plat Nomor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Model</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kapasitas</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Energi</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($row = $all_bus->fetch()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['plat_nomor'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['model'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $row['kapasitas'] ?> kursi</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                        $status_class = [
                                            'Tersedia' => 'bg-green-100 text-green-800',
                                            'Perjalanan' => 'bg-yellow-100 text-yellow-800',
                                            'Perbaikan' => 'bg-red-100 text-red-800'
                                        ];
                                        ?>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class[$row['status_parkir']] ?>">
                                        <?= $row['status_parkir'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-purple-600 h-2 rounded-full"
                                                style="width: <?= $row['level_energi'] ?>%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600"><?= $row['level_energi'] ?>%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editBus(<?= htmlspecialchars(json_encode($row)) ?>)"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Yakin ingin menghapus bus ini?')">
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
                    <i class="fas fa-bus text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada data bus</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="busModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900 mb-4">Tambah Bus Baru</h3>

                <form id="busForm" method="POST">
                    <input type="hidden" id="busId" name="id">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                            <input type="text" id="plat_nomor" name="plat_nomor" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Model Bus</label>
                            <input type="text" id="model" name="model" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kapasitas</label>
                            <input type="number" id="kapasitas" name="kapasitas" required min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fasilitas</label>
                            <textarea id="fasilitas" name="fasilitas" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="AC, WiFi, Toilet, dll."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Parkir</label>
                            <select id="status_parkir" name="status_parkir" required
                                class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Perjalanan">Perjalanan</option>
                                <option value="Perbaikan">Perbaikan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Level Energi (%)</label>
                            <input type="number" id="level_energi" name="level_energi" required min="0" max="100"
                                step="0.1"
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
        const modal = document.getElementById('busModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('busForm');
        const submitBtn = document.getElementById('submitBtn');

        if (action === 'create') {
            title.textContent = 'Tambah Bus Baru';
            form.reset();
            submitBtn.name = 'create';
            submitBtn.textContent = 'Simpan';
            document.getElementById('busId').value = '';
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('busModal').classList.add('hidden');
    }

    function editBus(bus) {
        openModal('edit');
        document.getElementById('modalTitle').textContent = 'Edit Bus';
        document.getElementById('busId').value = bus.id;
        document.getElementById('plat_nomor').value = bus.plat_nomor;
        document.getElementById('model').value = bus.model;
        document.getElementById('kapasitas').value = bus.kapasitas;
        document.getElementById('fasilitas').value = bus.fasilitas;
        document.getElementById('status_parkir').value = bus.status_parkir;
        document.getElementById('level_energi').value = bus.level_energi;

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.name = 'update';
        submitBtn.textContent = 'Update';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('busModal');
        if (event.target === modal) {
            closeModal();
        }
    }

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