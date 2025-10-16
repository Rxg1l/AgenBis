<?php
// Include autoloader atau require manual
require_once "config/Database.php";
require_once "models/Jadwal.php";
require_once "models/User.php"; // Tambahkan ini
require_once "controllers/AuthController.php";

$database = new Database();
$db = $database->getConnection();

// Inisialisasi AuthController
$auth = new AuthController($db);

$jadwal = new Jadwal($db);
$jadwal_terbaru = $jadwal->readAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgenBis - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    .hero-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-purple-600 font-medium">Beranda</a>
                    <a href="jadwal.php" class="text-gray-600 hover:text-purple-600 font-medium">Jadwal</a>
                    <a href="#tentang" class="text-gray-600 hover:text-purple-600 font-medium">Tentang</a>
                    <a href="#kontak" class="text-gray-600 hover:text-purple-600 font-medium">Kontak</a>

                    <?php if($auth->isLoggedIn()): ?>
                    <?php if($auth->isAdmin()): ?>
                    <a href="admin/dashboard.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                        Dashboard Admin
                    </a>
                    <?php else: ?>
                    <a href="user/dashboard.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                        Dashboard
                    </a>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="flex space-x-4">
                        <a href="login.php" class="text-gray-600 hover:text-purple-600 font-medium">Login</a>
                        <a href="register.php"
                            class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            Daftar
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Temukan Perjalanan Terbaik Anda</h1>
            <p class="text-xl mb-8 opacity-90">Pesan tiket bus dengan mudah, cepat, dan aman. Ribuan penumpang telah
                mempercayai perjalanan mereka kepada kami.</p>

            <!-- Search Form -->
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
                <form action="jadwal.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kota Asal</label>
                        <input type="text" name="asal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                            placeholder="Kota asal">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kota Tujuan</label>
                        <input type="text" name="tujuan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                            placeholder="Kota tujuan">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                        <input type="date" name="tanggal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition font-bold">
                            Cari Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Mengapa Memilih AgenBis?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center card-hover p-6">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Aman & Terpercaya</h3>
                    <p class="text-gray-600">Sistem pembayaran yang aman dan terjamin keamanannya</p>
                </div>

                <div class="text-center card-hover p-6">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bolt text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Cepat & Mudah</h3>
                    <p class="text-gray-600">Proses pemesanan yang cepat hanya dalam beberapa langkah</p>
                </div>

                <div class="text-center card-hover p-6">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Layanan pelanggan siap membantu kapan saja</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Schedule -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Jadwal Terbaru</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                if($jadwal_terbaru && $jadwal_terbaru->rowCount() > 0):
                    while($row = $jadwal_terbaru->fetch()): 
                ?>
                <div class="bg-white rounded-lg shadow-md card-hover overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">
                                    <?= htmlspecialchars($row['kota_asal'] ?? '') ?> →
                                    <?= htmlspecialchars($row['kota_tujuan'] ?? '') ?></h3>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($row['nama_tipe'] ?? '') ?></p>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Tersedia</span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span><?= date('d M Y', strtotime($row['waktu_keberangkatan'])) ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                <span><?= date('H:i', strtotime($row['waktu_keberangkatan'])) ?> -
                                    <?= date('H:i', strtotime($row['waktu_tiba'])) ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-bus mr-2"></i>
                                <span><?= htmlspecialchars($row['model'] ?? '') ?>
                                    (<?= htmlspecialchars($row['plat_nomor'] ?? '') ?>)</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-purple-600">Rp
                                <?= number_format($row['harga'] ?? 0, 0, ',', '.') ?></span>
                            <a href="login.php"
                                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                                Pesan Sekarang
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <div class="col-span-3 text-center py-8">
                    <p class="text-gray-500">Tidak ada jadwal tersedia</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="text-center mt-8">
                <a href="jadwal.php"
                    class="bg-white text-purple-600 border border-purple-600 px-6 py-3 rounded-lg hover:bg-purple-50 transition font-bold">
                    Lihat Semua Jadwal
                </a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <!-- ... kode section jadwal terbaru ... -->
    </section>

    <!-- Tentang Kami Section (BARU) -->
    <section id="tentang" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Tentang AgenBis</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Platform pemesanan tiket bus terdepan yang
                    menghubungkan Anda dengan berbagai destinasi di Indonesia</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                        alt="Bus Travel" class="rounded-lg shadow-lg w-full h-64 object-cover">
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Misi Kami</h3>
                    <p class="text-gray-600 mb-4">
                        AgenBis hadir untuk memudahkan perjalanan Anda dengan menyediakan layanan pemesanan tiket bus
                        yang cepat, aman, dan terpercaya. Kami berkomitmen untuk memberikan pengalaman terbaik dalam
                        setiap perjalanan yang Anda tempuh.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">100% pembayaran aman</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Dukungan 24/7</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-700">Ribuan rute tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-2xl text-purple-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">100+ Rute</h4>
                    <p class="text-gray-600">Melayani berbagai rute di seluruh Indonesia</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-green-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">50.000+ Penumpang</h4>
                    <p class="text-gray-600">Telah melayani ribuan penumpang setiap bulannya</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bus text-2xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">200+ Bus</h4>
                    <p class="text-gray-600">Bekerja sama dengan berbagai operator bus terpercaya</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak Section (BARU) -->
    <section id="kontak" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Hubungi Kami</h2>
                <p class="text-xl text-gray-600">Butuh bantuan? Tim support kami siap membantu Anda</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Informasi Kontak</h3>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Alamat Kantor</h4>
                                <p class="text-gray-600">Jl. Sudirman No. 123<br>Jakarta Pusat, 10210</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <i class="fas fa-phone text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Telepon</h4>
                                <p class="text-gray-600">+62 21 1234 5678<br>+62 21 1234 5679</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Email</h4>
                                <p class="text-gray-600">info@agenbis.com<br>support@agenbis.com</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-orange-100 p-3 rounded-full mr-4">
                                <i class="fas fa-clock text-orange-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Jam Operasional</h4>
                                <p class="text-gray-600">Senin - Minggu: 24/7<br>Customer Service: 08.00 - 22.00 WIB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mt-8">
                        <h4 class="font-semibold text-gray-800 mb-4">Follow Kami</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-gray-800 text-white p-3 rounded-full hover:bg-gray-700 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="bg-blue-400 text-white p-3 rounded-full hover:bg-blue-500 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="bg-pink-600 text-white p-3 rounded-full hover:bg-pink-700 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Kirim Pesan</h3>

                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                    placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                    placeholder="Masukkan email">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                placeholder="Subjek pesan">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea rows="5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                placeholder="Tulis pesan Anda..."></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-purple-600 text-white py-3 px-4 rounded-md hover:bg-purple-700 transition font-bold">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section (Opsional) -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Pertanyaan Umum</h2>
                <p class="text-xl text-gray-600">Temukan jawaban untuk pertanyaan yang sering diajukan</p>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Bagaimana cara memesan tiket?</h4>
                    <p class="text-gray-600">Pilih rute dan tanggal, login/register, pilih jumlah tiket, dan selesaikan
                        pembayaran.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Apakah bisa refund tiket?</h4>
                    <p class="text-gray-600">Tiket yang sudah dibeli tidak dapat di-refund. Pastikan jadwal Anda sebelum
                        memesan.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Bagaimana jika bus delay?</h4>
                    <p class="text-gray-600">Kami akan menginformasikan melalui SMS/email dan memberikan solusi terbaik.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-bus text-2xl text-purple-400 mr-3"></i>
                        <span class="text-xl font-bold">AgenBis</span>
                    </div>
                    <p class="text-gray-400">Platform pemesanan tiket bus terpercaya untuk perjalanan Anda.</p>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="index.php" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="jadwal.php" class="hover:text-white transition">Jadwal</a></li>
                        <li><a href="#tentang" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#kontak" class="hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Layanan</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Bus Ekspres</a></li>
                        <li><a href="#" class="hover:text-white transition">Bus Kota</a></li>
                        <li><a href="#" class="hover:text-white transition">Bus VIP</a></li>
                        <li><a href="#" class="hover:text-white transition">Bus Ekonomi</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Kontak</h4>
                    <div class="space-y-2 text-gray-400">
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            <span>+62 21 1234 5678</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@agenbis.com</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3"></i>
                            <span>Jakarta, Indonesia</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 AgenBis. All rights reserved.</p>
            </div>
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

        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });

    // Set min date untuk input tanggal
    document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>

</html>