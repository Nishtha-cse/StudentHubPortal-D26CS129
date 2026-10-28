    <?php
    // db.php - MySQL connection (PDO)
    $DB_HOST = 'localhost';
    $DB_NAME = 'student_api_db';
    $DB_USER = 'root';   // change if needed
    $DB_PASS = '';       // XAMPP default is empty

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
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }