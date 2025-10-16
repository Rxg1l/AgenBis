<?php
class Pemesanan {
    private $conn;
    private $table_name = "pemesanan";

    public $id;
    public $kode_booking;
    public $user_id;
    public $jadwal_id;
    public $jumlah_tiket;
    public $total_harga;
    public $status_pembayaran;
    public $metode_pembayaran;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $this->kode_booking = $this->generateKodeBooking();
        
        $query = "INSERT INTO " . $this->table_name . "
                  SET kode_booking=:kode_booking, user_id=:user_id, jadwal_id=:jadwal_id,
                      jumlah_tiket=:jumlah_tiket, total_harga=:total_harga,
                      status_pembayaran='Pending', waktu_kadaluarsa=DATE_ADD(NOW(), INTERVAL 2 HOUR)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":kode_booking", $this->kode_booking);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":jadwal_id", $this->jadwal_id);
        $stmt->bindParam(":jumlah_tiket", $this->jumlah_tiket);
        $stmt->bindParam(":total_harga", $this->total_harga);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    private function generateKodeBooking() {
        return 'AB' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    public function readByUser($user_id) {
        $query = "SELECT p.*, j.waktu_keberangkatan, j.waktu_tiba, r.kota_asal, r.kota_tujuan,
                         b.model, t.nama_tipe
                  FROM " . $this->table_name . " p
                  INNER JOIN jadwal j ON p.jadwal_id = j.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  INNER JOIN bus b ON j.bus_id = b.id
                  INNER JOIN tipe_tiket t ON j.tipe_tiket_id = t.id
                  WHERE p.user_id = ?
                  ORDER BY p.waktu_pemesanan DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT p.*, 
                         j.waktu_keberangkatan, j.waktu_tiba, j.harga,
                         r.kota_asal, r.kota_tujuan, r.jarak_km, r.waktu_tempuh_jam,
                         b.model, b.plat_nomor,
                         t.nama_tipe, 
                         u.nama as nama_user, u.email, u.telepon
                  FROM " . $this->table_name . " p
                  INNER JOIN jadwal j ON p.jadwal_id = j.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  INNER JOIN bus b ON j.bus_id = b.id
                  INNER JOIN tipe_tiket t ON j.tipe_tiket_id = t.id
                  INNER JOIN users u ON p.user_id = u.id
                  WHERE p.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . "
                  SET status_pembayaran=:status_pembayaran,
                      metode_pembayaran=:metode_pembayaran
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status_pembayaran", $this->status_pembayaran);
        $stmt->bindParam(":metode_pembayaran", $this->metode_pembayaran);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    // Method untuk laporan
    public function readAll() {
        $query = "SELECT p.*, j.waktu_keberangkatan, r.kota_asal, r.kota_tujuan
                  FROM " . $this->table_name . " p
                  INNER JOIN jadwal j ON p.jadwal_id = j.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  ORDER BY p.waktu_pemesanan DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readRecent($limit = 5) {
        $query = "SELECT p.*, j.waktu_keberangkatan, r.kota_asal, r.kota_tujuan
                  FROM " . $this->table_name . " p
                  INNER JOIN jadwal j ON p.jadwal_id = j.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  ORDER BY p.waktu_pemesanan DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getTotalRevenue($start_date, $end_date) {
        $query = "SELECT SUM(total_harga) as total 
                  FROM " . $this->table_name . " 
                  WHERE status_pembayaran = 'Success' 
                  AND DATE(waktu_pemesanan) BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $start_date);
        $stmt->bindParam(2, $end_date);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?: 0;
    }

    public function getTotalPassengers($start_date, $end_date) {
        $query = "SELECT SUM(jumlah_tiket) as total 
                  FROM " . $this->table_name . " 
                  WHERE status_pembayaran = 'Success' 
                  AND DATE(waktu_pemesanan) BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $start_date);
        $stmt->bindParam(2, $end_date);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?: 0;
    }

    public function getAveragePassengers($start_date, $end_date) {
        $query = "SELECT AVG(jumlah_tiket) as average 
                  FROM " . $this->table_name . " 
                  WHERE status_pembayaran = 'Success' 
                  AND DATE(waktu_pemesanan) BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $start_date);
        $stmt->bindParam(2, $end_date);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average'] ?: 0;
    }

    // Tambahkan method ini ke class Pemesanan yang sudah ada

public function createWithKursi($kursi_dipilih) {
    $this->kode_booking = $this->generateKodeBooking();
    
    // Start transaction
    $this->conn->beginTransaction();
    
    try {
        // Insert pemesanan
        $query = "INSERT INTO " . $this->table_name . "
                  SET kode_booking=:kode_booking, user_id=:user_id, jadwal_id=:jadwal_id,
                      jumlah_tiket=:jumlah_tiket, total_harga=:total_harga,
                      status_pembayaran='Pending', waktu_kadaluarsa=DATE_ADD(NOW(), INTERVAL 2 HOUR)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":kode_booking", $this->kode_booking);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":jadwal_id", $this->jadwal_id);
        $stmt->bindParam(":jumlah_tiket", $this->jumlah_tiket);
        $stmt->bindParam(":total_harga", $this->total_harga);

        if(!$stmt->execute()) {
            throw new Exception("Gagal membuat pemesanan");
        }

        $pemesanan_id = $this->conn->lastInsertId();

        // Insert detail pemesanan untuk setiap kursi
        foreach($kursi_dipilih as $kursi) {
            $query_detail = "INSERT INTO detail_pemesanan 
                            SET pemesanan_id=:pemesanan_id, nomor_kursi=:nomor_kursi,
                                nama_penumpang=:nama_penumpang, no_identitas=:no_identitas,
                                harga=:harga, status_tiket='Aktif', status_kursi='Dipesan'";
            
            $stmt_detail = $this->conn->prepare($query_detail);
            $stmt_detail->bindParam(":pemesanan_id", $pemesanan_id);
            $stmt_detail->bindParam(":nomor_kursi", $kursi['nomor_kursi']);
            $stmt_detail->bindParam(":nama_penumpang", $kursi['nama_penumpang']);
            $stmt_detail->bindParam(":no_identitas", $kursi['no_identitas']);
            $stmt_detail->bindParam(":harga", $kursi['harga']);
            
            if(!$stmt_detail->execute()) {
                throw new Exception("Gagal memesan kursi " . $kursi['nomor_kursi']);
            }
        }

        // Update kursi tersedia di jadwal
        $query_update = "UPDATE jadwal 
                        SET kursi_tersedia = kursi_tersedia - ? 
                        WHERE id = ?";
        
        $stmt_update = $this->conn->prepare($query_update);
        $stmt_update->bindParam(1, $this->jumlah_tiket);
        $stmt_update->bindParam(2, $this->jadwal_id);
        
        if(!$stmt_update->execute()) {
            throw new Exception("Gagal update kursi tersedia");
        }

        // Commit transaction
        $this->conn->commit();
        return $pemesanan_id;

    } catch (Exception $e) {
        // Rollback transaction jika ada error
        $this->conn->rollback();
        error_log("Error dalam pemesanan: " . $e->getMessage());
        return false;
    }
}
}
?>