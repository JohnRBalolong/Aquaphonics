<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: ../index.php');// Redirect to login page if not logged in
    exit();
}

// Access the admin's session data
$adminId = $_SESSION['admin']['uid'];
$adminEmail = $_SESSION['admin']['email'];


?>
