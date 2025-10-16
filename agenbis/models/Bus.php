<?php
class Bus {
    private $conn;
    private $table_name = "bus";

    public $id;
    public $plat_nomor;
    public $model;
    public $kapasitas;
    public $fasilitas;
    public $status_parkir;
    public $level_energi;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET plat_nomor=:plat_nomor, model=:model, kapasitas=:kapasitas,
                      fasilitas=:fasilitas, status_parkir=:status_parkir, level_energi=:level_energi";

        $stmt = $this->conn->prepare($query);

        $this->plat_nomor = htmlspecialchars(strip_tags($this->plat_nomor));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->fasilitas = htmlspecialchars(strip_tags($this->fasilitas));

        $stmt->bindParam(":plat_nomor", $this->plat_nomor);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":kapasitas", $this->kapasitas);
        $stmt->bindParam(":fasilitas", $this->fasilitas);
        $stmt->bindParam(":status_parkir", $this->status_parkir);
        $stmt->bindParam(":level_energi", $this->level_energi);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET plat_nomor=:plat_nomor, model=:model, kapasitas=:kapasitas,
                      fasilitas=:fasilitas, status_parkir=:status_parkir, level_energi=:level_energi
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->plat_nomor = htmlspecialchars(strip_tags($this->plat_nomor));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->fasilitas = htmlspecialchars(strip_tags($this->fasilitas));

        $stmt->bindParam(":plat_nomor", $this->plat_nomor);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":kapasitas", $this->kapasitas);
        $stmt->bindParam(":fasilitas", $this->fasilitas);
        $stmt->bindParam(":status_parkir", $this->status_parkir);
        $stmt->bindParam(":level_energi", $this->level_energi);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMostPopularBus($start_date, $end_date) {
    $query = "SELECT b.model, COUNT(p.id) as jumlah_pemesanan
              FROM bus b
              INNER JOIN jadwal j ON b.id = j.bus_id
              INNER JOIN pemesanan p ON j.id = p.jadwal_id
              WHERE p.status_pembayaran = 'Success'
              AND DATE(p.waktu_pemesanan) BETWEEN ? AND ?
              GROUP BY b.id
              ORDER BY jumlah_pemesanan DESC
              LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $start_date);
    $stmt->bindParam(2, $end_date);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
?>