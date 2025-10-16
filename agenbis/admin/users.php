<?php
require_once "../config/Database.php";
require_once "../models/User.php";
require_once "../controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();
$auth = new AuthController($db);
$auth->requireAdmin();

$user = new User($db);

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $user->nama = $_POST['nama'];
        $user->email = $_POST['email'];
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->role = $_POST['role'];
        $user->no_telepon = $_POST['no_telepon'];
        
        if ($user->create()) {
            header("Location: users.php?success=created");
            exit();
        }
    } elseif ($action === 'update') {
        $user->id = $_POST['id'];
        $user->nama = $_POST['nama'];
        $user->email = $_POST['email'];
        $user->role = $_POST['role'];
        $user->no_telepon = $_POST['no_telepon'];
        
        // Update password only if provided
        if (!empty($_POST['password'])) {
            $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        if ($user->update()) {
            header("Location: users.php?success=updated");
            exit();
        }
    } elseif ($action === 'delete') {
        $user->id = $_POST['id'];
        if ($user->delete()) {
            header("Location: users.php?success=deleted");
            exit();
        }
    }
}

// Get all users
$users = $user->readAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - AgenBis Admin</title>
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
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h1>
            <button onclick="openModal('create')"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">
                <i class="fas fa-plus mr-2"></i>Tambah Pengguna
            </button>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php
                $messages = [
                    'created' => 'Pengguna berhasil ditambahkan!',
                    'updated' => 'Pengguna berhasil diperbarui!',
                    'deleted' => 'Pengguna berhasil dihapus!'
                ];
                echo $messages[$_GET['success']] ?? 'Operasi berhasil!';
                ?>
        </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Daftar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($user_data = $users->fetch()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                            src="https://ui-avatars.com/api/?name=<?= urlencode($user_data['nama']) ?>&background=purple&color=white"
                                            alt="<?= $user_data['nama'] ?>">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= $user_data['nama'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $user_data['email'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $user_data['no_telepon'] ?? '-' ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full <?= 
                                    $user_data['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                                ?>">
                                    <?= ucfirst($user_data['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= date('d M Y', strtotime($user_data['created_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openModal('edit', <?= htmlspecialchars(json_encode($user_data)) ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <?php if ($user_data['role'] !== 'admin'): ?>
                                <button onclick="confirmDelete(<?= $user_data['id'] ?>)"
                                    class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
                <form id="userForm" method="POST" class="mt-4 space-y-4">
                    <input type="hidden" name="id" id="userId">
                    <input type="hidden" name="action" id="formAction">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                            placeholder="<?= $action === 'edit' ? 'Kosongkan jika tidak ingin mengubah' : '' ?>">
                        <?php if ($action === 'edit'): ?>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">No Telepon</label>
                        <input type="text" name="no_telepon" id="noTelepon"
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
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
                <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin menghapus pengguna ini?</p>

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
        const modal = document.getElementById('userModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('userForm');

        document.getElementById('formAction').value = action;

        if (action === 'create') {
            title.textContent = 'Tambah Pengguna Baru';
            form.reset();
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
        } else if (action === 'edit' && data) {
            title.textContent = 'Edit Pengguna';
            document.getElementById('userId').value = data.id;
            document.getElementById('nama').value = data.nama;
            document.getElementById('email').value = data.email;
            document.getElementById('noTelepon').value = data.no_telepon || '';
            document.getElementById('role').value = data.role;
            document.getElementById('password').required = false;
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
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
        const modal = document.getElementById('userModal');
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