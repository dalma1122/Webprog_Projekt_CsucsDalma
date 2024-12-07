<?php
include_once '../models/PatientRecord.php';
include_once '../config/Database.php';

class PatientRecordController {
    private $db;
    private $patient_record;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->patient_record = new PatientRecord($this->db);
    }

    // Ez a metódus egy orvos nevéhez tartozó páciens adatokat kérdez le
    public function getPatientRecordsByDoctor($doctor_name) {
        return $this->patient_record->readByDoctor($doctor_name);
    }

    // Ez a metódus egy adott pácienshez tartozó rekordokat adja vissza
    public function getPatientRecordsByPatient($patient_id) {
        return $this->patient_record->readByPatient($patient_id);
    }
}
?>
