<?php
class Consultation {
    private $conn;
    private $table_name = 'online_consultation';

    public $consultation_id;
    public $doctor_id;
    public $patient_id;
    public $email;
    public $question;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Az új konzultáció adatainak mentése az adatbázisba
    public function create() {
        $query = 'INSERT INTO ' . $this->table_name . ' SET doctors_doctor_id=:doctor_id, patients_patient_id=:patient_id, email=:email, question=:question, created_at=NOW()';

        $stmt = $this->conn->prepare($query);

        // htmlspecialchars() és strip_tags() biztosítják, hogy az adatok biztonságosak legyenek.
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->question = htmlspecialchars(strip_tags($this->question));

        $stmt->bindParam(':doctor_id', $this->doctor_id);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':question', $this->question);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
