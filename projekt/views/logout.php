<?php
include_once '../session.php';

session_start(); // Munkamenetet elinditása
session_unset(); // Eltávolítja az összes munkamenet változót
session_destroy(); // Bezárja a munkamenetet

// Átirányítás a bejelentkezési oldalra
header("Location: login.php");
exit;
?>
