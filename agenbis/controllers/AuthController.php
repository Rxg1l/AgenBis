<?php
session_start();

// Include required models - menggunakan absolute path
require_once __DIR__ . "/../models/User.php";

class AuthController {
    private $user;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->user = new User($db);
    }

    public function login($email, $password) {
        $this->user->email = $email;
        $this->user->password = $password;

        if($this->user->login()) {
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_nama'] = $this->user->nama;
            $_SESSION['user_role'] = $this->user->role;
            $_SESSION['user_foto'] = $this->user->foto_profil;
            
            return ['success' => true, 'role' => $this->user->role];
        } else {
            return ['success' => false, 'message' => 'Email atau password salah'];
        }
    }

    public function register($data) {
        $this->user->nama = $data['nama'];
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];
        $this->user->telepon = $data['telepon'];
        $this->user->alamat = $data['alamat'];

        if($this->user->emailExists()) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }

        if($this->user->register()) {
            return ['success' => true, 'message' => 'Registrasi berhasil. Silakan login.'];
        } else {
            return ['success' => false, 'message' => 'Registrasi gagal'];
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function requireAuth() {
        if(!$this->isLoggedIn()) {
            header("Location: ../login.php");
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireAuth();
        if(!$this->isAdmin()) {
            header("Location: ../index.php");
            exit;
        }
    }
}
?>