<?php
include_once 'config/Database.php';

// Kapcsolódás az adatbázishoz
$database = new Database();
$db = $database->getConnection();

// Felhasználók beszúrása
$users = [
    ['name' => 'Dr. Kovács István', 'email' => 'kovacs@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Szabó Péter', 'email' => 'szabo@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Tóth László', 'email' => 'toth@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Kiss Mária', 'email' => 'kiss@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Nagy János', 'email' => 'nagy@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Horváth Zoltán', 'email' => 'horvath@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Varga József', 'email' => 'varga@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Balogh Csaba', 'email' => 'balogh@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Molnár Ágnes', 'email' => 'molnar@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Dr. Farkas István', 'email' => 'farkas@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'doctor'],
    ['name' => 'Nagy Anna', 'email' => 'nagy.anna@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Kiss Béla', 'email' => 'kiss.bela@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Tóth Éva', 'email' => 'toth.eva@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Szabó Gábor', 'email' => 'szabo.gabor@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Horváth Máté', 'email' => 'horvath.mate@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Varga Katalin', 'email' => 'varga.katalin@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Balogh Réka', 'email' => 'balogh.reka@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Molnár Dániel', 'email' => 'molnar.daniel@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Farkas Tamás', 'email' => 'farkas.tamas@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient'],
    ['name' => 'Kovács Ilona', 'email' => 'kovacs.ilona@gmail.com', 'password' => password_hash('jelszo123', PASSWORD_BCRYPT), 'role' => 'patient']
];

$user_ids = []; //Ez egy üres tömb, amelybe a későbbiekben a beszúrt felhasználók azonosítói (ID-k) kerülnek.
foreach ($users as $user) {
    $query = "INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $user['name']);
    $stmt->bindParam(':email', $user['email']);
    $stmt->bindParam(':password', $user['password']);
    $stmt->bindParam(':role', $user['role']);
    if ($stmt->execute()) {
        $user_ids[] = $db->lastInsertId();
    }
}

// Orvosok beszúrása
$doctors = [
    ['users_users_id' => $user_ids[0], 'specialization' => 'kardiológus', 'experience_years' => 10],
    ['users_users_id' => $user_ids[1], 'specialization' => 'neurológus', 'experience_years' => 12],
    ['users_users_id' => $user_ids[2], 'specialization' => 'ortopéd', 'experience_years' => 8],
    ['users_users_id' => $user_ids[3], 'specialization' => 'bőrgyógyász', 'experience_years' => 15],
    ['users_users_id' => $user_ids[4], 'specialization' => 'sebész', 'experience_years' => 20],
    ['users_users_id' => $user_ids[5], 'specialization' => 'belgyógyász', 'experience_years' => 25],
    ['users_users_id' => $user_ids[6], 'specialization' => 'gasztroenterológus', 'experience_years' => 7],
    ['users_users_id' => $user_ids[7], 'specialization' => 'fül-orr-gégész', 'experience_years' => 5],
    ['users_users_id' => $user_ids[8], 'specialization' => 'gyermekorvos', 'experience_years' => 10],
    ['users_users_id' => $user_ids[9], 'specialization' => 'reumatológus', 'experience_years' => 12]
];

$doctor_ids = []; //Ez egy üres tömb, amelybe a későbbiekben a beszúrt orvosok azonosítói (ID-k) kerülnek.
foreach ($doctors as $doctor) {
    $query = "INSERT INTO doctors (users_users_id, specialization, experience_years) VALUES (:users_users_id, :specialization, :experience_years)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':users_users_id', $doctor['users_users_id']);
    $stmt->bindParam(':specialization', $doctor['specialization']);
    $stmt->bindParam(':experience_years', $doctor['experience_years']);
    if ($stmt->execute()) { 
        $doctor_ids[] = $db->lastInsertId(); 
    }
}

// Páciensek beszúrása
$patients = [
    ['users_users_id' => $user_ids[10], 'birt_date' => '1990-01-01', 'contact_number' => '0712345678'],
    ['users_users_id' => $user_ids[11], 'birt_date' => '1985-02-02', 'contact_number' => '07301234567'],
    ['users_users_id' => $user_ids[12], 'birt_date' => '1980-03-03', 'contact_number' => '07201234567'],
    ['users_users_id' => $user_ids[13], 'birt_date' => '1975-04-04', 'contact_number' => '07304567890'],
    ['users_users_id' => $user_ids[14], 'birt_date' => '1970-05-05', 'contact_number' => '07401234567'],
    ['users_users_id' => $user_ids[15], 'birt_date' => '1965-06-06', 'contact_number' => '07501234567'],
    ['users_users_id' => $user_ids[16], 'birt_date' => '1960-07-07', 'contact_number' => '07601234567'],
    ['users_users_id' => $user_ids[17], 'birt_date' => '1955-08-08', 'contact_number' => '07701234567'],
    ['users_users_id' => $user_ids[18], 'birt_date' => '1950-09-09', 'contact_number' => '07801234567'],
    ['users_users_id' => $user_ids[19], 'birt_date' => '1945-10-10', 'contact_number' => '07901234567']
];

$patient_ids = []; //Ez egy üres tömb, amelybe a későbbiekben a beszúrt páciensek azonosítói (ID-k) kerülnek.
foreach ($patients as $patient) {
    $query = "INSERT INTO patients (users_users_id, contact_number, birt_date) VALUES (:users_users_id, :contact_number, :birt_date)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':users_users_id', $patient['users_users_id']);
    $stmt->bindParam(':birt_date', $patient['birt_date']);
    $stmt->bindParam(':contact_number', $patient['contact_number']);
    if ($stmt->execute()) { 
        $patient_ids[] = $db->lastInsertId(); 
    }
}

// Időpontok beszúrása
$appointments = [
    ['patients_patient_id' => $patient_ids[0], 'doctors_doctor_id' => $doctor_ids[0], 'appointment_date' => '2024-12-02', 'appointment_hour' => '10:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[1], 'doctors_doctor_id' => $doctor_ids[1], 'appointment_date' => '2024-12-02', 'appointment_hour' => '11:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[2], 'doctors_doctor_id' => $doctor_ids[2], 'appointment_date' => '2024-12-03', 'appointment_hour' => '12:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[3], 'doctors_doctor_id' => $doctor_ids[3], 'appointment_date' => '2024-12-04', 'appointment_hour' => '13:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[4], 'doctors_doctor_id' => $doctor_ids[4], 'appointment_date' => '2024-12-05', 'appointment_hour' => '14:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[5], 'doctors_doctor_id' => $doctor_ids[5], 'appointment_date' => '2024-12-06', 'appointment_hour' => '15:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[6], 'doctors_doctor_id' => $doctor_ids[6], 'appointment_date' => '2024-12-07', 'appointment_hour' => '16:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[7], 'doctors_doctor_id' => $doctor_ids[7], 'appointment_date' => '2024-12-08', 'appointment_hour' => '17:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[8], 'doctors_doctor_id' => $doctor_ids[8], 'appointment_date' => '2024-12-09', 'appointment_hour' => '18:00', 'status' => 'booked'],
    ['patients_patient_id' => $patient_ids[9], 'doctors_doctor_id' => $doctor_ids[9], 'appointment_date' => '2024-12-10', 'appointment_hour' => '19:00', 'status' => 'booked']
];

foreach ($appointments as $appointment) {
    $query = "INSERT INTO appointments (doctors_doctor_id, patients_patient_id, appointment_date, appointment_hour, status, created_at) VALUES (:doctors_doctor_id, :patients_patient_id, :appointment_date, :appointment_hour, :status, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':patients_patient_id', $appointment['patients_patient_id']);
    $stmt->bindParam(':doctors_doctor_id', $appointment['doctors_doctor_id']);
    $stmt->bindParam(':appointment_date', $appointment['appointment_date']);
    $stmt->bindParam(':appointment_hour', $appointment['appointment_hour']);
    $stmt->bindParam(':status', $appointment['status']);
    $stmt->execute();
}

echo "Adatok sikeresen beszúrva!";
?>
