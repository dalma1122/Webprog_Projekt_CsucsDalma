<?php
include_once '../config/Database.php';
include_once '../models/User.php';
include_once '../models/Doctor.php';
include_once '../models/Patient.php';
include_once '../session.php';

class UserController {
    private $db;
    private $user;
    private $doctor;
    private $patient;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->doctor = new Doctor($this->db);
        $this->patient = new Patient($this->db);
    }

    // Ez a metódus új felhasználó regisztrációját kezeli
    public function register() {
        if ($_POST) {
            // Az users táblába bejegyzi az új felhasználót.
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];
            $this->user->role = $_POST['role'];
    
            // Az doctors vagy patients táblába menti a szerepkör-specifikus adatokat.
            if ($lastInsertId = $this->user->register()) {
                if ($this->user->role == 'doctor') {
                    $this->doctor->user_id = $lastInsertId;
                    $this->doctor->specialization = $_POST['specialization'];
                    $this->doctor->experience_years = $_POST['experience_years'];
                    $this->doctor->create();
                } elseif ($this->user->role == 'patient') {
                    $this->patient->user_id = $lastInsertId;
                    $this->patient->birth_date = $_POST['birth_date'];
                    $this->patient->contact_number = $_POST['contact_number'];
                    $this->patient->create();
                }
                echo 'Sikeres regisztráció!';
                // A befejezés után a login.php oldalra irányítja a felhasználót.
                header('Location: ../views/login.php');
                exit;
            } else {
                echo 'Hiba történt: regisztráció sikertelen.';
            }
        }
    }    

    // Ez a metódus a felhasználói bejelentkezésért felel
    public function login() {
        // Ellenőrzi az email és jelszó kombinációt a users táblában.
        if (isset($_POST["login"])) {
            $this->user->email = $_POST['email'];
            $this->user->password = $_POST['password'];

            // Siker esetén létrehozza a felhasználói munkamenetet.
            if ($this->user->login()) {
                $_SESSION['user_id'] = $this->user->user_id;
                $_SESSION['name'] = $this->user->name;
                $_SESSION['email'] = $this->user->email;
                $_SESSION['role'] = $this->user->role;

                // Sikeres bejelentkezés esetén átirányít az appointment.php oldalra.
                header('Location: ../views/appointment.php');
                exit;
            } else {
                echo 'Helytelen email vagy jelszó!';
            }
        }
    }
}
?>
