<?php
echo "Checking required files...<br><br>";

$required_files = [
    'config/Database.php',
    'models/User.php',
    'models/Bus.php',
    'models/Rute.php',
    'models/Jadwal.php',
    'models/Pemesanan.php',
    'models/TipeTiket.php',
    'controllers/AuthController.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file - EXISTS<br>";
    } else {
        echo "❌ $file - MISSING<br>";
    }
}

echo "<br>Current directory: " . __DIR__;
?>