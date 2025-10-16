<?php
class DenahKursi {
    private $conn;
    private $table_name = "denah_kursi";

    public $id;
    public $bus_id;
    public $nomor_kursi;
    public $baris;
    public $kolom;
    public $tipe_kursi;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get semua kursi untuk bus tertentu
    public function getKursiByBus($bus_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE bus_id = ? 
                  ORDER BY baris, kolom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bus_id);
        $stmt->execute();
        
        return $stmt;
    }

    // Get kursi yang tersedia untuk jadwal tertentu
    public function getKursiTersedia($jadwal_id) {
        $query = "SELECT dk.* 
                  FROM denah_kursi dk
                  INNER JOIN jadwal j ON dk.bus_id = j.bus_id
                  WHERE j.id = ? 
                  AND dk.status = 'Tersedia'
                  AND dk.nomor_kursi NOT IN (
                      SELECT dp.nomor_kursi 
                      FROM detail_pemesanan dp
                      INNER JOIN pemesanan p ON dp.pemesanan_id = p.id
                      WHERE p.jadwal_id = ? 
                      AND dp.status_kursi = 'Dipesan'
                      AND p.status_pembayaran IN ('Pending', 'Success')
                  )
                  ORDER BY dk.baris, dk.kolom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $jadwal_id);
        $stmt->bindParam(2, $jadwal_id);
        $stmt->execute();
        
        return $stmt;
    }

    // Cek apakah kursi tersedia
    public function isKursiTersedia($nomor_kursi, $jadwal_id) {
        $query = "SELECT COUNT(*) as total
                  FROM denah_kursi dk
                  INNER JOIN jadwal j ON dk.bus_id = j.bus_id
                  WHERE j.id = ? 
                  AND dk.nomor_kursi = ?
                  AND dk.status = 'Tersedia'
                  AND dk.nomor_kursi NOT IN (
                      SELECT dp.nomor_kursi 
                      FROM detail_pemesanan dp
                      INNER JOIN pemesanan p ON dp.pemesanan_id = p.id
                      WHERE p.jadwal_id = ? 
                      AND dp.status_kursi = 'Dipesan'
                      AND p.status_pembayaran IN ('Pending', 'Success')
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $jadwal_id);
        $stmt->bindParam(2, $nomor_kursi);
        $stmt->bindParam(3, $jadwal_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Get kursi yang sudah dipesan untuk jadwal tertentu
    public function getKursiTerbooking($jadwal_id) {
        $query = "SELECT dp.nomor_kursi
                  FROM detail_pemesanan dp
                  INNER JOIN pemesanan p ON dp.pemesanan_id = p.id
                  WHERE p.jadwal_id = ? 
                  AND dp.status_kursi = 'Dipesan'
                  AND p.status_pembayaran IN ('Pending', 'Success')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $jadwal_id);
        $stmt->execute();
        
        $booked_seats = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $booked_seats[] = $row['nomor_kursi'];
        }
        return $booked_seats;
    }
}
?>