```php
<?php
session_start();
require_once "db.php";

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

// Allow only students
if ($_SESSION["role"] !== "student") {
    http_response_code(403);
    die("Unauthorized Access: Students only.");
}

// Get last login timestamp
$stmt = $conn->prepare(
    "SELECT last_login FROM users WHERE id = ?"
);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$lastLogin = $row["last_login"] ?? null;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>
        Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
    </h2>

    <p>
        Your role:
        <strong>student</strong>
    </p>

    <p>
        Last Login:
        <strong>
            <?php
            echo $lastLogin
                ? htmlspecialchars($lastLogin)
                : "First login";
            ?>
        </strong>
    </p>

    <h3>Student Dashboard</h3>

    <p>Welcome to the Student Dashboard.</p>

    <a href="dashboard.php">Go to Student Dashboard</a>

    <br><br>

    <a href="logout.php">Logout</a>

</div>

</body>
</html>
```