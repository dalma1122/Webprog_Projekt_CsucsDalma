<?php
include_once 'config/Database.php';

// Kapcsolódás az adatbázishoz
$database = new Database();
$db = $database->getConnection();

// Orvosok és páciensek azonosítói lekérdezés adatbázisból
$doctor_ids = [];
$patient_ids = [];


$query = "SELECT doctor_id, users_users_id FROM doctors";
$stmt = $db->prepare($query);
$stmt->execute();
//Lekéri az összes orvos azonosítóját és az users táblához tartozó felhasználói azonosítót
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $doctor_ids[] = ['doctor_id' => $row['doctor_id'], 'user_id' => $row['users_users_id']];
}

$query = "SELECT patient_id FROM patients";
$stmt = $db->prepare($query);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $patient_ids[] = $row['patient_id'];
}

// Ha kevesebb mint 10 orvos vagy páciens található az adatbázisban, a script leál
//Miért van erre szükség? Mert a kód feltételezi, hogy legalább 10 orvos és páciens van, hogy a patient record-ok beszúrása megfelelően működjön.
if (count($doctor_ids) < 10 || count($patient_ids) < 10) {
    die("Nem elegendő orvos vagy páciens az adatbázisban.");
}

// Patient Records beszúrása
$patient_records = [
    // Három orvosnak van négy páciense
    ['patients_patient_id' => $patient_ids[0], 'doctor_name' => 'Dr. Kovács István', 'description' => 'Vizsgálat', 'record_date' => '2024-01-01'],
    ['patients_patient_id' => $patient_ids[1], 'doctor_name' => 'Dr. Kovács István', 'description' => 'Kezelés', 'record_date' => '2024-01-02'],
    ['patients_patient_id' => $patient_ids[2], 'doctor_name' => 'Dr. Kovács István', 'description' => 'Műtét', 'record_date' => '2024-01-03'],
    ['patients_patient_id' => $patient_ids[3], 'doctor_name' => 'Dr. Kovács István', 'description' => 'Kontroll', 'record_date' => '2024-01-04'],

    ['patients_patient_id' => $patient_ids[4], 'doctor_name' => 'Dr. Szabó Péter', 'description' => 'Vizsgálat', 'record_date' => '2024-01-05'],
    ['patients_patient_id' => $patient_ids[5], 'doctor_name' => 'Dr. Szabó Péter', 'description' => 'Kezelés', 'record_date' => '2024-01-06'],
    ['patients_patient_id' => $patient_ids[6], 'doctor_name' => 'Dr. Szabó Péter', 'description' => 'Műtét', 'record_date' => '2024-01-07'],
    ['patients_patient_id' => $patient_ids[7], 'doctor_name' => 'Dr. Szabó Péter', 'description' => 'Kontroll', 'record_date' => '2024-01-08'],

    ['patients_patient_id' => $patient_ids[8], 'doctor_name' => 'Dr. Tóth László', 'description' => 'Vizsgálat', 'record_date' => '2024-01-09'],
    ['patients_patient_id' => $patient_ids[9], 'doctor_name' => 'Dr. Tóth László', 'description' => 'Kezelés', 'record_date' => '2024-01-10'],
    ['patients_patient_id' => $patient_ids[0], 'doctor_name' => 'Dr. Tóth László', 'description' => 'Műtét', 'record_date' => '2024-01-11'],
    ['patients_patient_id' => $patient_ids[1], 'doctor_name' => 'Dr. Tóth László', 'description' => 'Kontroll', 'record_date' => '2024-01-12'],

    // Két orvosnak van három páciense
    ['patients_patient_id' => $patient_ids[2], 'doctor_name' => 'Dr. Kiss Mária', 'description' => 'Vizsgálat', 'record_date' => '2024-01-13'],
    ['patients_patient_id' => $patient_ids[3], 'doctor_name' => 'Dr. Kiss Mária', 'description' => 'Kezelés', 'record_date' => '2024-01-14'],
    ['patients_patient_id' => $patient_ids[4], 'doctor_name' => 'Dr. Kiss Mária', 'description' => 'Műtét', 'record_date' => '2024-01-15'],

    ['patients_patient_id' => $patient_ids[5], 'doctor_name' => 'Dr. Nagy János', 'description' => 'Kontroll', 'record_date' => '2024-01-16'],
    ['patients_patient_id' => $patient_ids[6], 'doctor_name' => 'Dr. Nagy János', 'description' => 'Vizsgálat', 'record_date' => '2024-01-17'],
    ['patients_patient_id' => $patient_ids[7], 'doctor_name' => 'Dr. Nagy János', 'description' => 'Kezelés', 'record_date' => '2024-01-18'],

    // Két orvosnak csak két páciense van
    ['patients_patient_id' => $patient_ids[8], 'doctor_name' => 'Dr. Horváth Zoltán', 'description' => 'Műtét', 'record_date' => '2024-01-19'],
    ['patients_patient_id' => $patient_ids[9], 'doctor_name' => 'Dr. Horváth Zoltán', 'description' => 'Kontroll', 'record_date' => '2024-01-20'],

    ['patients_patient_id' => $patient_ids[0], 'doctor_name' => 'Dr. Varga József', 'description' => 'Vizsgálat', 'record_date' => '2024-01-21'],
    ['patients_patient_id' => $patient_ids[1], 'doctor_name' => 'Dr. Varga József', 'description' => 'Kezelés', 'record_date' => '2024-01-22'],

    // Két orvosnak csak egy páciense van
    ['patients_patient_id' => $patient_ids[2], 'doctor_name' => 'Dr. Balogh Csaba', 'description' => 'Műtét', 'record_date' => '2024-01-23'],
    
    ['patients_patient_id' => $patient_ids[3], 'doctor_name' => 'Dr. Molnár Ágnes', 'description' => 'Kontroll', 'record_date' => '2024-01-24']
];

foreach ($patient_records as $record) {
    $query = "INSERT INTO patient_records (patients_patient_id, doctor_name, description, record_date, created_at) VALUES (:patients_patient_id, :doctor_name, :description, :record_date, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":patients_patient_id", $record["patients_patient_id"]);
    $stmt->bindParam(":doctor_name", $record["doctor_name"]);
    $stmt->bindParam(":description", $record["description"]);
    $stmt->bindParam(":record_date", $record["record_date"]);
    $stmt->execute();
}

echo "Patient records sikeresen beszúrva!";
?>
