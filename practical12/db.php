<?php
session_start();

$conn = new mysqli("localhost", "root", "", "event_db");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

// Check whether the admin is logged in.
function requireAdmin() {
    if (empty($_SESSION["admin_logged_in"])) {
        header("Location: login.php");
        exit;
    }
}

// Escape output before displaying it in HTML.
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>