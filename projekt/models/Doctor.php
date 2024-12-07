<?php
class Doctor {
    private $conn;
    private $table_name = "doctors";

    public $doctor_id;
    public $user_id;
    public $specialization;
    public $experience_years;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Az orvos hozzáadása az adatbázishoz
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET users_users_id=:user_id, specialization=:specialization, experience_years=:experience_years";

        $stmt = $this->conn->prepare($query);

        // htmlspecialchars() és strip_tags() biztosítják, hogy az adatok biztonságosak legyenek.
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->specialization = htmlspecialchars(strip_tags($this->specialization));
        $this->experience_years = htmlspecialchars(strip_tags($this->experience_years));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":specialization", $this->specialization);
        $stmt->bindParam(":experience_years", $this->experience_years);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Az összes orvost lekérdezi, és az orvosi szakterületet a users táblával való összekapcsolással adja vissza.
    public function readAll() {
        $query = "SELECT d.doctor_id, u.name, d.specialization 
                  FROM " . $this->table_name . " d 
                  JOIN users u ON d.users_users_id = u.users_id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Az összes egyedi szakterületet lekérdezi a doctors táblából.
    public function readSpecializations() {
        $query = "SELECT DISTINCT specialization FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // A lekérdezés visszaadja azokat az orvosokat, akik egy adott szakterületen dolgoznak.
    public function readBySpecialization($specialization) {
        $query = "SELECT d.doctor_id, u.name 
                  FROM " . $this->table_name . " d 
                  JOIN users u ON d.users_users_id = u.users_id
                  WHERE d.specialization = :specialization";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->execute();
        return $stmt;
    }

    // Az orvos azonosítóját adja vissza, amelyet egy felhasználói azonosító alapján keres meg.
    public function getDoctorIdByUserId($user_id) {
        $query = "SELECT doctor_id FROM " . $this->table_name . " WHERE users_users_id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['doctor_id'] : null;
    }
}
?>
