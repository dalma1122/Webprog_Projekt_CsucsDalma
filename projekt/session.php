<?php
session_start();

//Ellenőrzi, hogy a felhasználó be van-e jelentkezve.
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

//Ellenőrzi, hogy a bejelentkezett felhasználó orvos-e.
function isDoctor() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'doctor';
}

//Ellenőrzi, hogy a bejelentkezett felhasználó páciens-e.
function isPatient() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'patient';
}
?>
