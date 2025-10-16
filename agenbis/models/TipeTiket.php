<?php
class TipeTiket {
    private $conn;
    private $table_name = "tipe_tiket";


    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>