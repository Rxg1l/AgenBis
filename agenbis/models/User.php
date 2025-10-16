<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nama;
    public $email;
    public $password;
    public $telepon;
    public $alamat;
    public $role;
    public $foto_profil;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        $query = "SELECT id, nama, email, password, role, foto_profil 
                  FROM " . $this->table_name . " 
                  WHERE email = ? AND role IN ('penumpang', 'admin')
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->nama = $row['nama'];
                $this->role = $row['role'];
                $this->foto_profil = $row['foto_profil'];
                return true;
            }
        }
        return false;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nama=:nama, email=:email, password=:password, 
                      telepon=:telepon, alamat=:alamat, role='penumpang'";

        $stmt = $this->conn->prepare($query);

        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telepon = htmlspecialchars(strip_tags($this->telepon));
        $this->alamat = htmlspecialchars(strip_tags($this->alamat));

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":telepon", $this->telepon);
        $stmt->bindParam(":alamat", $this->alamat);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getProfile() {
        $query = "SELECT id, nama, email, telepon, alamat, foto_profil, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile() {
        $query = "UPDATE " . $this->table_name . "
                  SET nama=:nama, telepon=:telepon, alamat=:alamat, 
                      foto_profil=:foto_profil, updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->telepon = htmlspecialchars(strip_tags($this->telepon));
        $this->alamat = htmlspecialchars(strip_tags($this->alamat));

        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":telepon", $this->telepon);
        $stmt->bindParam(":alamat", $this->alamat);
        $stmt->bindParam(":foto_profil", $this->foto_profil);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>