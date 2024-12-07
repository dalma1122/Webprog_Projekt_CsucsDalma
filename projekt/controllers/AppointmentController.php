<?php
include_once '../config/Database.php';
include_once '../models/Appointment.php';
include_once '../models/Doctor.php';
include_once '../models/Patient.php';
include_once '../session.php';

class AppointmentController {
    private $db; // Az adatbázis kapcsolat
    private $appointment; // Az időpontokkal kapcsolatos modellek kezelésére
    private $doctor; // Az orvosokkal kapcsolatos modellek kezelésére
    private $patient; // A páciensekkel kapcsolatos modellek kezelésére

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->appointment = new Appointment($this->db);
        $this->doctor = new Doctor($this->db);
        $this->patient = new Patient($this->db);
    }

    // Ez a metódus kezeli a felhasználók által indított időpont foglalást.
    public function createAppointment() {
        if ($_POST && isset($_POST['create'])) {

            // Ha az aktuális felhasználó orvos, időpont foglalása nem engedélyezett.
            if ($_SESSION['role'] === 'doctor') {
                $_SESSION['error_message'] = 'Orvosként nem foglalhat időpontot!';
                header('Location: appointment.php');
                exit;
            }

            $this->appointment->doctor_id = $_POST['doctor_id'];
            $user_id = $_SESSION['user_id'];
            $this->appointment->patient_id = $this->patient->getPatientIdByUserId($user_id);
            $this->appointment->appointment_date = $_POST['appointment_date'];
            $this->appointment->appointment_hour = $_POST['appointment_hour'];

            // Ha a kívánt időpont már foglalt, hibát jelez és a formot újratölti.
            if ($this->appointment->isBooked($this->appointment->doctor_id, $this->appointment->appointment_date, $this->appointment->appointment_hour)) {
                $_SESSION['error_message'] = 'Az időpont már foglalt';
                $_SESSION['form_data'] = $_POST;
                header('Location: appointment.php');
                exit;
            }

            // Óra beáll1tása romániai időzónára
            date_default_timezone_set('Europe/Bucharest');
            $current_date = date('Y-m-d'); // Dátum formátuma
            $current_time = date('H:i'); // Óra formátuma

            // Múltbeli időpontok kizárása
            if ($this->appointment->appointment_date < $current_date || ($this->appointment->appointment_date == $current_date && $this->appointment->appointment_hour < $current_time)) {
                $_SESSION['error_message'] = 'Nem lehet a múltba időpontot foglalni!';
                header('Location: appointment.php');
                exit;
            }

            if ($this->appointment->create()) {
                $_SESSION['success_message'] = 'Időpont sikeresen létrehozva!';
                header('Location: appointment.php');
                exit;
            } else {
                $_SESSION['error_message'] = 'Hiba történt az időpont létrehozása során.';
            }
        }
    }

    // Időpont lemondása, azonosítja a törlendő időpontot a POST kérésből és hívja a modell megfelelő metódusát
    public function cancelAppointment() {
        if ($_POST && isset($_POST['cancel'])) {
            $appointment_id = $_POST['appointment_id'];
            if ($this->appointment->cancel($appointment_id)) {
                $_SESSION['success_message'] = 'Időpont sikeresen lemondva!';
                header('Location: appointment.php');
                exit;
            } else {
                $_SESSION['error_message'] = 'Hiba történt az időpont lemondása során.';
            }
        }
    }

    // Specializációk lekérdezése
    public function getSpecializations() {
        return $this->doctor->readSpecializations();
    }

    // Orvosok adott specializáció alapján
    public function getDoctorsBySpecialization($specialization) {
        return $this->doctor->readBySpecialization($specialization);
    }

    // Minden időpont
    public function getAllAppointments() {
        return $this->appointment->readAll();
    }

    // Felhasználó időpontjai
    public function getUserAppointments() {
        $user_id = $_SESSION['user_id'];
        if ($_SESSION['role'] === 'patient') {
            return $this->appointment->readByPatientId($user_id);
        }
        return null;
    }

    // Orvosok lekérdezése
    public function getDoctors() {
        return $this->doctor->readAll();
    }
}
?>
