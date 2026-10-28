<?php
// api.php - JSON API for students (list, add, update, delete)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once 'db.php';

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Optional artificial delay (ms) to demo the loading state: api.php?delay=800
if (isset($_GET['delay'])) {
    usleep(min((int)$_GET['delay'], 3000) * 1000);
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

function validate($d) {
    $name   = trim($d['name']   ?? '');
    $email  = trim($d['email']  ?? '');
    $course = trim($d['course'] ?? '');
    if ($name === '' || $email === '' || $course === '') {
        respond(['success' => false, 'message' => 'Name, email and course are required'], 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(['success' => false, 'message' => 'Invalid email address'], 400);
    }
    return [$name, $email, $course];
}

try {
    switch ($method) {

        // LIST (all) or single record with ?id=
        case 'GET':
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
                $stmt->execute([(int)$_GET['id']]);
                $row = $stmt->fetch();
                if (!$row) respond(['success' => false, 'message' => 'Student not found'], 404);
                respond(['success' => true, 'data' => $row]);
            }
            $rows = $pdo->query('SELECT * FROM students ORDER BY id DESC')->fetchAll();
            respond(['success' => true, 'data' => $rows]);
            break;

        // ADD
        case 'POST':
            [$name, $email, $course] = validate($input);
            $stmt = $pdo->prepare('INSERT INTO students (name, email, course) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $course]);
            respond([
                'success' => true,
                'message' => 'Student added',
                'id'      => (int)$pdo->lastInsertId(),
            ], 201);
            break;

        // UPDATE
        case 'PUT':
            $id = (int)($input['id'] ?? 0);
            if ($id <= 0) respond(['success' => false, 'message' => 'Valid id is required'], 400);
            [$name, $email, $course] = validate($input);
            $stmt = $pdo->prepare('UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?');
            $stmt->execute([$name, $email, $course, $id]);
            respond(['success' => true, 'message' => 'Student updated']);
            break;

        // DELETE
        case 'DELETE':
            $id = (int)($input['id'] ?? ($_GET['id'] ?? 0));
            if ($id <= 0) respond(['success' => false, 'message' => 'Valid id is required'], 400);
            $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                respond(['success' => false, 'message' => 'Student not found'], 404);
            }
            respond(['success' => true, 'message' => 'Student deleted']);
            break;

        default:
            respond(['success' => false, 'message' => 'Method not allowed'], 405);
    }
} catch (PDOException $e) {
    // 1062 = duplicate entry (unique email)
    if ($e->errorInfo[1] == 1062) {
        respond(['success' => false, 'message' => 'Email already exists'], 409);
    }
    respond(['success' => false, 'message' => 'Server error'], 500);
}