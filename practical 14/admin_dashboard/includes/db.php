<?php
// includes/db.php : PDO database connection
$DB_HOST = 'localhost';
$DB_NAME = 'admin_dashboard';
$DB_USER = 'root';   // XAMPP default
$DB_PASS = '';       // XAMPP default (empty)

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Check includes/db.php and import schema.sql.');
}
