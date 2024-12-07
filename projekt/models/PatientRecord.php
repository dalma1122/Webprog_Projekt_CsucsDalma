<?php
class PatientRecord {
    private $conn;
    private $table_name = 'patient_records';

    public $record_id;
    public $patient_id;
    public $doctor_name;
    public $description;
    public $record_date;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Rekordok lekérdezése orvos neve alapján
    public function readByDoctor($doctor_name) {
        // A lekérdezés az orvos neve alapján szűri le a rekordokat, és visszaadja a páciens nevét, az orvos nevét, a rekord leírását és dátumá
        $query = 'SELECT p.name as patient_name, pr.doctor_name, pr.description, pr.record_date
                  FROM ' . $this->table_name . ' pr
                  JOIN patients pat ON pr.patients_patient_id = pat.patient_id
                  JOIN users p ON pat.users_users_id = p.users_id
                  WHERE pr.doctor_name = :doctor_name';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_name', $doctor_name);
        $stmt->execute();
        return $stmt;
    }

    // Rekordok lekérdezése páciens azonosítója alapján
    public function readByPatient($patient_id) {
        // A visszaadott információk tartalmazzák a páciens nevét, az orvos nevét, a rekord leírását és dátumát.
        $query = 'SELECT p.name as patient_name, pr.doctor_name, pr.description, pr.record_date
                  FROM ' . $this->table_name . ' pr
                  JOIN patients pat ON pr.patients_patient_id = pat.patient_id
                  JOIN users p ON pat.users_users_id = p.users_id
                  WHERE pr.patients_patient_id = :patient_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
