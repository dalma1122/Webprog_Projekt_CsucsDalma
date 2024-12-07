<?php
include_once '../config/Database.php';
include_once '../models/Consultation.php';
include_once '../models/Doctor.php';
include_once '../models/Patient.php';
include_once '../session.php';

class ConsultationController {
    private $db;
    private $consultation;
    private $doctor;
    private $patient;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->consultation = new Consultation($this->db);
        $this->doctor = new Doctor($this->db);
        $this->patient = new Patient($this->db);
    }

    // Konzultáció Létrehozása
    public function createConsultation() {
        // A felhasználó beküldi a kérdést a kiválasztott orvoshoz.
        if ($_POST){
            $this->consultation->doctor_id = $_POST['doctor_id'];
            $user_id = $_SESSION['user_id'];
            // A felhasználó azonosítójából (user_id) lekérdezi a páciens azonosítóját (patient_id).
            $this->consultation->patient_id = $this->patient->getPatientIdByUserId($user_id);
            $this->consultation->email = $_POST['email'];
            $this->consultation->question = $_POST['question'];

            // Az adatokat a create() metódus menti az adatbázisba.
            if ($this->consultation->create()) {
                $_SESSION['success_message'] = "Sikeresen elküldve! Hamorosan megkapja válaszát email-ben.";
                header("Location: consultation.php");
                exit;
            } else {
                $_SESSION["error_message"] = "Hiba történt a küldés során, próbálja újra!";
            }
        }  
    }

    // Specializációk lekérdezése
    public function getSpecializations() {
        return $this->doctor->readSpecializations();
    }

    // Orvosok lekérdezése specializáció alapján
    public function getDoctorsBySpecialization($specialization) {
        return $this->doctor->readBySpecialization($specialization);
    }

    // Orvosok lekérdezése
    public function getDoctors() {
        return $this->doctor->readAll();
    }
}
?>