<?php
class Rute {
    private $conn;
    private $table_name = "rute";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCities() {
        $query = "SELECT kota_asal FROM rute 
                  UNION 
                  SELECT kota_tujuan FROM rute 
                  ORDER BY kota_asal";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $cities = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cities[] = $row['kota_asal'];
        }
        return $cities;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY kota_asal, kota_tujuan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Method untuk admin
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET kota_asal=:kota_asal, kota_tujuan=:kota_tujuan, 
                      jarak_km=:jarak_km, waktu_tempuh_jam=:waktu_tempuh_jam";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":kota_asal", $this->kota_asal);
        $stmt->bindParam(":kota_tujuan", $this->kota_tujuan);
        $stmt->bindParam(":jarak_km", $this->jarak_km);
        $stmt->bindParam(":waktu_tempuh_jam", $this->waktu_tempuh_jam);

        return $stmt->execute();
    }
}
?>