<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php'); // Redirect to login page if not logged in
    exit();
}

// Get user session data
$uid = $_SESSION['user']['uid'];
$email = $_SESSION['user']['email'];
$folderName = $_SESSION['user']['folderName'];
$owner = $_SESSION['user']['owner'];
$phone = $_SESSION['user']['phone'];
$doin = $_SESSION['user']['doin'];

?>