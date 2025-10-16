<?php
require_once "../config/Database.php";
require_once "../models/Rute.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$rute = new Rute($db);

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $rute->kota_asal = $_POST['kota_asal'];
        $rute->kota_tujuan = $_POST['kota_tujuan'];
        $rute->jarak = $_POST['jarak'];
        $rute->durasi = $_POST['durasi'];
        $rute->harga = $_POST['harga'];
        
        if ($rute->create()) {
            header("Location: rute.php?success=created");
            exit();
        }
    } elseif ($action === 'update') {
        $rute->id = $_POST['id'];
        $rute->kota_asal = $_POST['kota_asal'];
        $rute->kota_tujuan = $_POST['kota_tujuan'];
        $rute->jarak = $_POST['jarak'];
        $rute->durasi = $_POST['durasi'];
        $rute->harga = $_POST['harga'];
        
        if ($rute->update()) {
            header("Location: rute.php?success=updated");
            exit();
        }
    } elseif ($action === 'delete') {
        $rute->id = $_POST['id'];
        if ($rute->delete()) {
            header("Location: rute.php?success=deleted");
            exit();
        }
    }
}

// Get all routes
$routes = $rute->readAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Rute - AgenBis Admin</title>
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
                    <a href="../index.php" class="text-gray-600 hover:text-purple-600">Beranda</a>
                    <a href="../logout.php" class="text-gray-600 hover:text-purple-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Rute</h1>
            <button onclick="openModal('create')"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">
                <i class="fas fa-plus mr-2"></i>Tambah Rute
            </button>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php
                $messages = [
                    'created' => 'Rute berhasil ditambahkan!',
                    'updated' => 'Rute berhasil diperbarui!',
                    'deleted' => 'Rute berhasil dihapus!'
                ];
                echo $messages[$_GET['success']] ?? 'Operasi berhasil!';
                ?>
        </div>
        <?php endif; ?>

        <!-- Routes Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kota Asal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kota Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jarak (km)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Durasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($route = $routes->fetch()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= $route['kota_asal'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= $route['kota_tujuan'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $route['jarak'] ?> km</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $route['durasi'] ?> jam</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Rp <?= number_format($route['harga'], 0, ',', '.') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openModal('edit', <?= htmlspecialchars(json_encode($route)) ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="confirmDelete(<?= $route['id'] ?>)"
                                    class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="routeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
                <form id="routeForm" method="POST" class="mt-4 space-y-4">
                    <input type="hidden" name="id" id="routeId">
                    <input type="hidden" name="action" id="formAction">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kota Asal</label>
                        <input type="text" name="kota_asal" id="kotaAsal" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kota Tujuan</label>
                        <input type="text" name="kota_tujuan" id="kotaTujuan" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jarak (km)</label>
                        <input type="number" name="jarak" id="jarak" required step="0.1"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Durasi (jam)</label>
                        <input type="number" name="durasi" id="durasi" required step="0.1"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" name="harga" id="harga" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-300">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin menghapus rute ini?</p>

                <form id="deleteForm" method="POST" class="mt-4">
                    <input type="hidden" name="id" id="deleteId">
                    <input type="hidden" name="action" value="delete">

                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-300">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openModal(action, data = null) {
        const modal = document.getElementById('routeModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('routeForm');

        document.getElementById('formAction').value = action;

        if (action === 'create') {
            title.textContent = 'Tambah Rute Baru';
            form.reset();
            document.getElementById('routeId').value = '';
        } else if (action === 'edit' && data) {
            title.textContent = 'Edit Rute';
            document.getElementById('routeId').value = data.id;
            document.getElementById('kotaAsal').value = data.kota_asal;
            document.getElementById('kotaTujuan').value = data.kota_tujuan;
            document.getElementById('jarak').value = data.jarak;
            document.getElementById('durasi').value = data.durasi;
            document.getElementById('harga').value = data.harga;
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('routeModal').classList.add('hidden');
    }

    function confirmDelete(id) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('routeModal');
        const deleteModal = document.getElementById('deleteModal');

        if (event.target === modal) {
            closeModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    }
    </script>
</body>

</html>