<?php
include_once '../session.php';
include_once '../controllers/ConsultationController.php';

// A isLoggedIn() funkciót hívjuk meg, hogy ellenőrizzük, hogy a felhasználó be van-e jelentkezve. Ha nem, átirányítjuk őt a login.php oldalra.
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$controller = new ConsultationController(); // Példányosítás
$specializations = $controller->getSpecializations(); // Orvosi specializációk lekérése
$selectedSpecialization = isset($_POST['specialization']) ? $_POST['specialization'] : '';
$doctors = null;
// Ha a felhasználó kiválasztott egy szakirányt, akkor a getDoctorsBySpecialization() metódus lekérdezi azokat az orvosokat, akik az adott szakterületen dolgoznak.
if ($selectedSpecialization) {
    $doctors = $controller->getDoctorsBySpecialization($selectedSpecialization);
} else {
    $doctors = $controller->getDoctors(); // Ha nem lett kiválasztva szakirány, akkor az összes orvost lekérjük
}
// Amikor a felhasználó elküldi az űrlapot, és ha a submit gombot nyomta meg, akkor meghívódik a createConsultation() függvény, amely az új konzultációt az adatbázisba menti.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) { 
    $controller->createConsultation(); 
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online konzultáció</title>
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
            <div class="consultation">
                <h3>Tegye fel kérdését a megfelelő orvosnak:</h3><br>
                <form method="post" action="consultation.php">
                    <label for="specialization">Specializáció:</label><br>
                    <select name="specialization" id="specialization" required onchange="this.form.submit()">
                        <option value="">Válassz specializációt</option>
                        <?php while ($row = $specializations->fetch(PDO::FETCH_ASSOC)) : ?>
                            <option value="<?= htmlspecialchars($row['specialization']) ?>" <?= $selectedSpecialization == $row['specialization'] ? 'selected' : '' ?>><?= htmlspecialchars($row['specialization']) ?></option>
                        <?php endwhile; ?>
                    </select><br><br>
                </form>
                <form method="post" action="consultation.php">
                    <label for="doctor_id">Doktor:</label><br>
                    <select name="doctor_id" id="doctor_id" required>
                        <option value="">Válassz doktort</option>
                        <?php if ($doctors) : ?>
                            <?php while ($row = $doctors->fetch(PDO::FETCH_ASSOC)) : ?>
                                <option value="<?= htmlspecialchars($row['doctor_id']) ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select><br><br>
                    <label for="email">Email:</label><br>
                    <input type="email" name="email" id="email" required><br><br>
                    <label for="question">Tegye fel kérdését:</label><br>
                    <input type="textarea" name="question" id="question" required><br><br>
                    <button type="submit" name="submit">Küldés</button>
                </form>
                <?php 
                if (isset($_SESSION['error_message'])) { 
                    echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>'; 
                    unset($_SESSION['error_message']); 
                }
                if (isset($_SESSION['success_message'])) { 
                    echo '<p style="color: green;">' . htmlspecialchars($_SESSION['success_message']) . '</p>'; 
                    unset($_SESSION['success_message']); 
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
