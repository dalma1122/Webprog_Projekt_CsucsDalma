<?php
include_once '../controllers/UserController.php';

$controller = new UserController();
$controller->register();
?>

<form method="post" action="register.php">
    <h3>Regisztráció</h3>
    <label for="name">Név:</label><br>
    <input type="text" name="name" id="name" required><br><br>
    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" required><br><br>
    <label for="password">Jelszó:</label><br>
    <input type="password" name="password" id="password" required><br><br>
    <label for="role">Szerepkör:</label><br>
    <select name="role" id="role" onchange="toggleRoleFields()" required>
        <option value="">Válassz szerepkört</option>
        <option value="doctor">Doktor</option>
        <option value="patient">Páciens</option>
    </select><br><br>
    <div id="doctorFields" style="display: none;">
        <label for="specialization">Szakirány:</label><br>
        <input type="text" name="specialization" id="specialization"><br><br>
        <label for="experience_years">Tapasztalat (évek):</label><br>
        <input type="number" name="experience_years" id="experience_years" min="0"><br><br>
    </div>
    <div id="patientFields" style="display: none;">
        <label for="birth_date">Születési dátum:</label><br>
        <input type="date" name="birth_date" id="birth_date"><br><br>
        <label for="contact_number">Kapcsolattartási szám:</label><br>
        <input type="text" name="contact_number" id="contact_number"><br><br>
    </div>
    <button type="submit" name="register">Regisztráció</button>
</form>
<script>
    // onchange esemény segítségével váltogatja a látható mezőcsoportokat a szerepkör kiválasztása alapján
    function toggleRoleFields() {
        const role = document.getElementById("role").value;
        // Ha a felhasználó orvost választ, a doctorFields mezők jelennek meg, ha pácienst választ, akkor a patientFields mezők.
        document.getElementById("doctorFields").style.display = role === "doctor" ? "block" : "none";
        document.getElementById("patientFields").style.display = role === "patient" ? "block" : "none";
    }
</script>
