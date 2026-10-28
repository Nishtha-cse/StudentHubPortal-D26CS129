<?php
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === "admin" && $password === "Admin@123") {
        session_regenerate_id(true);
        $_SESSION["admin_logged_in"] = true;

        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<?php if ($error !== ""): ?>
    <p class="message error">
        <?= e($error) ?>
    </p>
<?php endif; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
    <div class="login-box">
        <h2>Event Admin Login</h2>

        <!-- Keep your existing PHP error message here -->

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>