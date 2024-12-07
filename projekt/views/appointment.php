<?php
include_once '../controllers/AppointmentController.php';
include_once '../session.php';

// A isLoggedIn() funkciót hívjuk meg, hogy ellenőrizzük, hogy a felhasználó be van-e jelentkezve. Ha nem, átirányítjuk őt a login.php oldalra.
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$controller = new AppointmentController(); // Példányosítás
$specializations = $controller->getSpecializations(); // Lekérdezi az orvosi szakirányokat az adatbázisból.
$selectedSpecialization = isset($_POST['specialization']) ? $_POST['specialization'] : '';
$doctors = null;
if ($selectedSpecialization) {
    //  Lekérdezi az orvosokat a kiválasztott szakirány alapján.
    $doctors = $controller->getDoctorsBySpecialization($selectedSpecialization);
} else {
    $doctors = $controller->getDoctors(); // Minden orvost lekérdezi.
}
$allAppointments = $controller->getAllAppointments(); // Az összes foglalt időpontot lekérdezi.
$controller->createAppointment(); // Létrehozza az új időpontot az adatbázisban
$controller->cancelAppointment(); // Lemondja az időpontot
$userAppointments = null;
if (isset($_POST['view_user_appointments'])) {
    $userAppointments = $controller->getUserAppointments(); //  A bejelentkezett felhasználó saját időpontjait lekérdezi.
}

$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Időpontfoglalás</title>
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
            <div class="appointment">
                <form method="post" action="appointment.php">
                    <label for="specialization">Specializáció:</label><br>
                    <select name="specialization" id="specialization" required onchange="this.form.submit()">
                        <option value="">Válassz specializációt</option>
                        <?php while ($row = $specializations->fetch(PDO::FETCH_ASSOC)) : ?>
                            <option value="<?= htmlspecialchars($row['specialization']) ?>" <?= $selectedSpecialization == $row['specialization'] ? 'selected' : '' ?>><?= htmlspecialchars($row['specialization']) ?></option>
                        <?php endwhile; ?>
                    </select><br><br>
                </form>

                <form method="post" action="appointment.php">
                    <label for="doctor_id">Doktor:</label><br>
                    <select name="doctor_id" id="doctor_id" required>
                        <option value="">Válassz doktort</option>
                        <?php if ($doctors) : ?>
                            <?php while ($row = $doctors->fetch(PDO::FETCH_ASSOC)) : ?>
                                <option value="<?= htmlspecialchars($row['doctor_id']) ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select><br><br>
                    <label for="patient_name">Páciens neve:</label><br>
                    <input type="text" name="patient_name" id="patient_name" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly><br><br>
                    <label for="appointment_date">Időpont dátuma:</label><br>
                    <input type="date" name="appointment_date" id="appointment_date" value="<?= isset($form_data['appointment_date']) ? htmlspecialchars($form_data['appointment_date']) : '' ?>" required><br><br>
                    <label for="appointment_hour">Időpont óra:</label><br>
                    <input type="time" name="appointment_hour" id="appointment_hour" value="<? isset($form_data['appointment_hour']) ? htmlspecialchars($form_data['appointment_hour']) : '' ?>" required><br><br>
                    <button type="submit" name="create">Időpontfoglalás</button>
                </form>

                <form method="post" action="appointment.php" style="margin-top: 20px;">
                    <button type="submit" name="view_user_appointments">Időpont lemondása</button>
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

            <?php if ($userAppointments !== null) : ?>
                <div class="appointments-list">
                    <h2>Saját időpontjaim:</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Orvos neve</th>
                                <th>Specializáció</th>
                                <th>Dátum</th>
                                <th>Óra</th>
                                <th>Státusz</th>
                                <th>Lemondás</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $userAppointments->fetch(PDO::FETCH_ASSOC)) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                    <td><?= htmlspecialchars($row['specialization']) ?></td>
                                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                    <td><?= htmlspecialchars($row['appointment_hour']) ?></td>
                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                    <td>
                                        <form method="post" action="appointment.php" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                                            <button type="submit" name="cancel">Lemondás</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <div class="appointments-list">
                <h2>Foglalt időpontok:</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Orvos neve</th>
                            <th>Specializáció</th>
                            <th>Dátum</th>
                            <th>Óra</th>
                            <th>Státusz</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $allAppointments->fetch(PDO::FETCH_ASSOC)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($row['specialization']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_hour']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
