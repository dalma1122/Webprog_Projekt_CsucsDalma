<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public $patient_id;
    public $user_id; // Az oszlop neve users_users_id az adatbázisban
    public $birth_date;
    public $contact_number;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Az új pácienst hozzáadja a patients táblához a megadott adatokkal
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET users_users_id=:user_id, birt_date=:birth_date, contact_number=:contact_number";

        $stmt = $this->conn->prepare($query);

        // htmlspecialchars() és strip_tags() biztosítják, hogy az adatok biztonságosak legyenek.
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":contact_number", $this->contact_number);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // A páciens azonosítójának lekérdezése egy felhasználó azonosítója alapján
    public function getPatientIdByUserId($user_id) {
        $query = "SELECT patient_id FROM " . $this->table_name . " WHERE users_users_id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['patient_id'] : null;
    }
}
?>
