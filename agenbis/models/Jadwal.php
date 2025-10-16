<?php
class Jadwal {
    private $conn;
    private $table_name = "jadwal";

    public $id;
    public $bus_id;
    public $rute_id;
    public $tipe_tiket_id;
    public $waktu_keberangkatan;
    public $waktu_tiba;
    public $harga;
    public $kursi_tersedia;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function search($kota_asal = '', $kota_tujuan = '', $tanggal = '') {
        $query = "SELECT j.*, 
                         b.model, b.plat_nomor, 
                         r.kota_asal, r.kota_tujuan, r.jarak_km, r.waktu_tempuh_jam,
                         t.nama_tipe, t.deskripsi
                  FROM " . $this->table_name . " j
                  INNER JOIN bus b ON j.bus_id = b.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  INNER JOIN tipe_tiket t ON j.tipe_tiket_id = t.id
                  WHERE j.status = 'Aktif' AND j.kursi_tersedia > 0";

        $params = [];

        if (!empty($kota_asal)) {
            $query .= " AND r.kota_asal LIKE ?";
            $params[] = "%$kota_asal%";
        }

        if (!empty($kota_tujuan)) {
            $query .= " AND r.kota_tujuan LIKE ?";
            $params[] = "%$kota_tujuan%";
        }

        if (!empty($tanggal)) {
            $query .= " AND DATE(j.waktu_keberangkatan) = ?";
            $params[] = $tanggal;
        }

        $query .= " ORDER BY j.waktu_keberangkatan ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function readAll() {
        $query = "SELECT j.*, 
                         b.model, b.plat_nomor, 
                         r.kota_asal, r.kota_tujuan, r.jarak_km, r.waktu_tempuh_jam,
                         t.nama_tipe
                  FROM " . $this->table_name . " j
                  INNER JOIN bus b ON j.bus_id = b.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  INNER JOIN tipe_tiket t ON j.tipe_tiket_id = t.id
                  ORDER BY j.waktu_keberangkatan DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT j.*, 
                         b.model, b.plat_nomor, b.kapasitas, b.fasilitas,
                         r.kota_asal, r.kota_tujuan, r.jarak_km, r.waktu_tempuh_jam,
                         t.nama_tipe, t.deskripsi
                  FROM " . $this->table_name . " j
                  INNER JOIN bus b ON j.bus_id = b.id
                  INNER JOIN rute r ON j.rute_id = r.id
                  INNER JOIN tipe_tiket t ON j.tipe_tiket_id = t.id
                  WHERE j.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
    // Validasi data sebelum insert
    if($this->harga < 1000) {
        throw new Exception("Harga minimal Rp 1.000");
    }
    
    if($this->kursi_tersedia < 1) {
        throw new Exception("Kursi tersedia minimal 1");
    }
    
    if(strtotime($this->waktu_keberangkatan) >= strtotime($this->waktu_tiba)) {
        throw new Exception("Waktu tiba harus setelah waktu keberangkatan");
    }

    $query = "INSERT INTO " . $this->table_name . "
              SET bus_id=:bus_id, rute_id=:rute_id, tipe_tiket_id=:tipe_tiket_id,
                  waktu_keberangkatan=:waktu_keberangkatan, waktu_tiba=:waktu_tiba,
                  harga=:harga, kursi_tersedia=:kursi_tersedia, status=:status";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":bus_id", $this->bus_id);
    $stmt->bindParam(":rute_id", $this->rute_id);
    $stmt->bindParam(":tipe_tiket_id", $this->tipe_tiket_id);
    $stmt->bindParam(":waktu_keberangkatan", $this->waktu_keberangkatan);
    $stmt->bindParam(":waktu_tiba", $this->waktu_tiba);
    $stmt->bindParam(":harga", $this->harga);
    $stmt->bindParam(":kursi_tersedia", $this->kursi_tersedia);
    $stmt->bindParam(":status", $this->status);

    return $stmt->execute();
}

    public function updateKursi() {
        $query = "UPDATE " . $this->table_name . " 
                  SET kursi_tersedia = kursi_tersedia - 1 
                  WHERE id = ? AND kursi_tersedia > 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Method untuk laporan
    public function getMostPopularRoute($start_date, $end_date) {
        $query = "SELECT r.kota_asal, r.kota_tujuan, 
                         COUNT(p.id) as jumlah_penumpang,
                         SUM(p.total_harga) as total_pendapatan
                  FROM rute r
                  INNER JOIN jadwal j ON r.id = j.rute_id
                  INNER JOIN pemesanan p ON j.id = p.jadwal_id
                  WHERE p.status_pembayaran = 'Success'
                  AND DATE(p.waktu_pemesanan) BETWEEN ? AND ?
                  GROUP BY r.id
                  ORDER BY jumlah_penumpang DESC
                  LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $start_date);
        $stmt->bindParam(2, $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>