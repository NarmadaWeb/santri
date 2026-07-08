<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

function checkAdmin() {
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../santri/dashboard.php");
        exit();
    }
}

function checkSantri() {
    checkLogin();
    if ($_SESSION['role'] !== 'santri') {
        header("Location: ../admin/dashboard.php");
        exit();
    }
}
?>
