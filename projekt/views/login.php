<?php
include_once '../controllers/UserController.php';

$controller = new UserController();
$controller->login(); // Az login() metódus meghívása történik, amely felelős a felhasználó bejelentkezésének kezeléséért
?>

<form method="post" action="login.php">
    <h3>Bejelentkezés</h3>
    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" required><br><br>
    <label for="password">Jelszó:</label><br>
    <input type="password" name="password" id="password" required><br><br>
    <button type="submit" name="login">Bejelentkezés</button>
    <button type="button" onclick="redirectToRegister()">Regisztráció</button>
</form>
<script>
    // Ez a funkció végzi el az átirányítást a regisztrációs oldalra, ha a felhasználó még nem regisztrált.
    function redirectToRegister() {
        window.location.href = 'register.php';
    }
</script>
