<?php
include_once '../session.php';
include_once '../config/Database.php';
include_once '../controllers/PatientRecordController.php';

// A isLoggedIn() funkciót hívjuk meg, hogy ellenőrizzük, hogy a felhasználó be van-e jelentkezve. Ha nem, átirányítjuk őt a login.php oldalra.
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Adatbázis kapcsolat létrehozása
$database = new Database();
$db = $database->getConnection();

$controller = new PatientRecordController();
$records = null;

// Ha az aktuális felhasználó orvos, akkor az orvos neve alapján lekérdezi az általa kezelt betegeket és azok adatait
if ($_SESSION['role'] == 'doctor') {
    $doctor_name = $_SESSION['name'];
    $records = $controller->getPatientRecordsByDoctor($doctor_name);
} 
// Ha a felhasználó páciens, akkor először lekérdezi a felhasználóhoz tartozó páciens azonosítóját a patients táblából, majd a páciens rekordjait kérdezi le.
else if ($_SESSION['role'] == 'patient') {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT patient_id FROM patients WHERE users_users_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $patient_id = $stmt->fetch(PDO::FETCH_ASSOC)['patient_id'];
    $records = $controller->getPatientRecordsByPatient($patient_id);
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betegadatok nyilvántartása</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="page">
        <header>
            <nav class="navigation-bar" id="navbar">
                <ul>
                    <li><a href="appointment.php">Időpontfoglalás</a></li>
                    <li><a href="consultation.php">Online konzultáció</a></li>
                    <li><a href="patientRecords.php">Betegadatok nyilvántartása</a></li>
                    <li><a href="logout.php">Kijelentkezés</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <div class="patient-records">
                <h2>Betegadatok</h2><br>
                <table>
                    <thead>
                        <tr>
                            <th>Páciens neve</th>
                            <th>Orvos neve</th>
                            <th>Leírás</th>
                            <th>Dátum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $records->fetch(PDO::FETCH_ASSOC)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= htmlspecialchars($row['record_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
