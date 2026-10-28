
<?php
session_start();
require_once "db.php";

// Check whether the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?error=Please login first.");
    exit;
}

// Check whether the user is an admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403);
    die("Unauthorized Access: Admins only.");
}

// Fetch the last login timestamp
$stmt = $conn->prepare(
    "SELECT last_login FROM users WHERE id = ?"
);

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$lastLogin = $row["last_login"] ?? null;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>
        Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
    </h2>

    <p>
        Your role:
        <strong>admin</strong>
    </p>

    <p>
        Last Login:
        <strong>
            <?php
            if (!empty($lastLogin)) {
                echo htmlspecialchars($lastLogin);
            } else {
                echo "First login";
            }
            ?>
        </strong>
    </p>

    <h3>Admin Dashboard</h3>

    <p>Welcome to the Admin Dashboard.</p>

    <a href="dashboard.php">Go to Admin Dashboard</a>

    <br><br>

    <a href="logout.php">Logout</a>

</div>

</body>
</html>
