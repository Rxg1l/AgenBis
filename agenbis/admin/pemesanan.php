<?php
require_once "../config/Database.php";
require_once "../models/Pemesanan.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$pemesanan = new Pemesanan($db);

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'update_status') {
        $pemesanan->id = $_POST['id'];
        $pemesanan->status_pembayaran = $_POST['status_pembayaran'];
        
        if ($pemesanan->updateStatus()) {
            header("Location: pemesanan.php?success=updated");
            exit();
        }
    } elseif ($action === 'delete') {
        $pemesanan->id = $_POST['id'];
        if ($pemesanan->delete()) {
            header("Location: pemesanan.php?success=deleted");
            exit();
        }
    }
}

// Get all bookings
$bookings = $pemesanan->readAllWithDetails();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemesanan - AgenBis Admin</title>
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
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pemesanan</h1>
            <p class="text-gray-600">Kelola semua pemesanan tiket bus</p>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $_GET['success'] === 'updated' ? 'Status pemesanan berhasil diperbarui!' : 'Pemesanan berhasil dihapus!' ?>
        </div>
        <?php endif; ?>

        <!-- Bookings Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rute</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Penumpang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($booking = $bookings->fetch()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= $booking['kode_booking'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= $booking['kota_asal'] ?> → <?= $booking['kota_tujuan'] ?>
                                </div>
                                <div class="text-xs text-gray-500"><?= $booking['tanggal_keberangkatan'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $booking['nama_penumpang'] ?></div>
                                <div class="text-xs text-gray-500"><?= $booking['email_penumpang'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= date('d M Y', strtotime($booking['created_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp
                                    <?= number_format($booking['total_harga'], 0, ',', '.') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full <?= 
                                    $booking['status_pembayaran'] == 'Success' ? 'bg-green-100 text-green-800' : 
                                    ($booking['status_pembayaran'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                ?>">
                                    <?= $booking['status_pembayaran'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openStatusModal(<?= htmlspecialchars(json_encode($booking)) ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-sync-alt"></i> Status
                                </button>
                                <button onclick="viewDetails(<?= htmlspecialchars(json_encode($booking)) ?>)"
                                    class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <button onclick="confirmDelete(<?= $booking['id'] ?>)"
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

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900">Update Status Pembayaran</h3>
                <form id="statusForm" method="POST" class="mt-4 space-y-4">
                    <input type="hidden" name="id" id="bookingId">
                    <input type="hidden" name="action" value="update_status">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                        <select name="status_pembayaran" id="statusPembayaran" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            <option value="Pending">Pending</option>
                            <option value="Success">Success</option>
                            <option value="Failed">Failed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeStatusModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-300">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900">Detail Pemesanan</h3>
                <div id="detailsContent" class="mt-4 space-y-3">
                    <!-- Details will be populated by JavaScript -->
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" onclick="closeDetailsModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin menghapus pemesanan ini?</p>

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
    function openStatusModal(booking) {
        document.getElementById('bookingId').value = booking.id;
        document.getElementById('statusPembayaran').value = booking.status_pembayaran;
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    function viewDetails(booking) {
        const content = document.getElementById('detailsContent');
        content.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold">Kode Booking:</label>
                    <p>${booking.kode_booking}</p>
                </div>
                <div>
                    <label class="font-semibold">Tanggal Pemesanan:</label>
                    <p>${new Date(booking.created_at).toLocaleDateString('id-ID')}</p>
                </div>
                <div>
                    <label class="font-semibold">Rute:</label>
                    <p>${booking.kota_asal} → ${booking.kota_tujuan}</p>
                </div>
                <div>
                    <label class="font-semibold">Tanggal Keberangkatan:</label>
                    <p>${booking.tanggal_keberangkatan}</p>
                </div>
                <div>
                    <label class="font-semibold">Penumpang:</label>
                    <p>${booking.nama_penumpang}</p>
                    <p class="text-sm text-gray-600">${booking.email_penumpang}</p>
                    <p class="text-sm text-gray-600">${booking.no_telepon_penumpang || '-'}</p>
                </div>
                <div>
                    <label class="font-semibold">Jumlah Penumpang:</label>
                    <p>${booking.jumlah_penumpang} orang</p>
                </div>
                <div>
                    <label class="font-semibold">Total Harga:</label>
                    <p class="font-bold">Rp ${parseInt(booking.total_harga).toLocaleString('id-ID')}</p>
                </div>
                <div>
                    <label class="font-semibold">Status:</label>
                    <span class="px-2 py-1 text-xs rounded-full ${
                        booking.status_pembayaran == 'Success' ? 'bg-green-100 text-green-800' : 
                        booking.status_pembayaran == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
                    }">
                        ${booking.status_pembayaran}
                    </span>
                </div>
            </div>
        `;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function confirmDelete(id) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const modals = ['statusModal', 'detailsModal', 'deleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
    </script>
</body>

</html>