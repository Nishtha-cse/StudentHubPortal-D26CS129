<?php 

 

require_once "db.php"; 

 

if ($_SERVER["REQUEST_METHOD"] !== "POST") { 

    die("Invalid request."); 

} 

 

// Get form data 

$username = trim($_POST["username"] ?? ""); 

$email = trim($_POST["email"] ?? ""); 

$password = $_POST["password"] ?? ""; 

$confirm_password = $_POST["confirm_password"] ?? ""; 

 

// Backend validation 

if ($username === "" || $email === "" || $password === "" || $confirm_password === "") { 

    die("All fields are required."); 

} 

 

// Validate username 

if (!preg_match("/^[a-zA-Z0-9_]{3,50}$/", $username)) { 

    die("Username must contain only letters, numbers, and underscore."); 

} 

 

// Validate email 

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 

    die("Please enter a valid email address."); 

} 

 

// Validate password length 

if (strlen($password) < 6) { 

    die("Password must contain at least 6 characters."); 

} 

 

// Confirm password 

if ($password !== $confirm_password) { 

    die("Passwords do not match."); 

} 

 

// Check duplicate username/email 

$check = $conn->prepare( 

    "SELECT id FROM users WHERE username = ? OR email = ?" 

); 

 

$check->bind_param("ss", $username, $email); 

$check->execute(); 

$check->store_result(); 

 

if ($check->num_rows > 0) { 

    $check->close(); 

    $conn->close(); 

    die("Username or email already exists."); 

} 

 

$check->close(); 

 

// Hash password 

$hashed_password = password_hash($password, PASSWORD_DEFAULT); 

 

// Insert user 

$stmt = $conn->prepare( 

    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)" 

); 

 

$stmt->bind_param( 

    "sss", 

    $username, 

    $email, 

    $hashed_password 

); 

 

if ($stmt->execute()) { 

 

    echo "<h2>Registration Successful!</h2>"; 

    echo "<p>User registered successfully.</p>"; 

 

} else { 

 

    echo "<h2>Registration Failed</h2>"; 

    echo "<p>Unable to register user.</p>"; 

} 

 

$stmt->close(); 

$conn->close(); 

 