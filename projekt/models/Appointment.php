<?php
class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $appointment_id;
    public $doctor_id;
    public $patient_id;
    public $appointment_date;
    public $appointment_hour;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Az időpontok rögzítése az adatbázisba
    public function create() {
        // Az alapértelmezett státusz booked. 
        // created_at: Az aktuális időbélyeg kerül mentésre
        $query = "INSERT INTO " . $this->table_name . " SET doctors_doctor_id=:doctor_id, patients_patient_id=:patient_id, appointment_date=:appointment_date, 
                  appointment_hour=:appointment_hour, status='booked', created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        // htmlspecialchars() és strip_tags() biztosítják, hogy az adatok biztonságosak legyenek.
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
        $this->appointment_hour = htmlspecialchars(strip_tags($this->appointment_hour));

        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":appointment_hour", $this->appointment_hour);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ez a metódus visszaadja az összes időpontot, orvosokkal összekapcsolva
    public function readAll() {
        // Csatlakoztatja az időpontokat az orvosok és felhasználók adataival. 
        // Az eredmény dátum és az idő szerint növekvő sorrendben jelenik meg
        $query = "SELECT a.appointment_id, u.name as doctor_name, d.specialization, a.appointment_date, a.appointment_hour, a.status 
                  FROM " . $this->table_name . " a
                  JOIN doctors d ON a.doctors_doctor_id = d.doctor_id
                  JOIN users u ON d.users_users_id = u.users_id
                  ORDER BY a.appointment_date ASC, a.appointment_hour ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Csak az adott pácienshez tartozó időpontokat adja vissza
    public function readByPatientId($user_id) {
        // Csak azok az időpontok jelennek meg, amelyek nincsenek lemondva
        $query = "SELECT a.appointment_id, u.name as doctor_name, d.specialization, a.appointment_date, a.appointment_hour, a.status 
                  FROM " . $this->table_name . " a
                  JOIN doctors d ON a.doctors_doctor_id = d.doctor_id
                  JOIN users u ON d.users_users_id = u.users_id
                  JOIN patients p ON a.patients_patient_id = p.patient_id
                  WHERE p.users_users_id = ? AND a.status != 'canceled'
                  ORDER BY a.appointment_date ASC, a.appointment_hour ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Egy meglévő időpontot „canceled” státuszra állít
    public function cancel($appointment_id) {
        $query = "UPDATE " . $this->table_name . " SET status='canceled' WHERE appointment_id=:appointment_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":appointment_id", $appointment_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ellenőrzi, hogy egy adott időpont foglalt-e
    public function isBooked($doctor_id, $appointment_date, $appointment_hour) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE doctors_doctor_id = :doctor_id AND appointment_date = :appointment_date 
                  AND appointment_hour = :appointment_hour AND status = 'booked'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':appointment_date', $appointment_date);
        $stmt->bindParam(':appointment_hour', $appointment_hour);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?>
