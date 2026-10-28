<?php 

 

session_start(); 

 

// Session timeout: 5 minutes 

$timeout = 300; 

 

if (!isset($_SESSION["user_id"])) { 

    header("Location: login.php?error=Please login first."); 

    exit; 

} 

 

if ( 

    isset($_SESSION["last_activity"]) && 

    (time() - $_SESSION["last_activity"] > $timeout) 

) { 

    session_unset(); 

    session_destroy(); 

 

    header("Location: login.php?error=Session expired. Please login again."); 

    exit; 

} 

 

// Update last activity time 

$_SESSION["last_activity"] = time(); 

 

?> 

 

<!DOCTYPE html> 

<html> 

<head> 

    <title>User Dashboard</title> 

    <link rel="stylesheet" href="style.css"> 

</head> 

 

<body> 

 

<div class="container"> 

 

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2> 

 

    <p> 

        Your role: 

        <strong> 

            <?php echo htmlspecialchars($_SESSION["role"]); ?> 

        </strong> 

    </p> 

    <p>
    Your role:
    <strong>
        <?php echo htmlspecialchars($_SESSION["role"]); ?>
    </strong>
</p>

    <?php if ($_SESSION["role"] === "admin"): ?> 

 

        <h3>Admin Dashboard</h3> 

 

        <p>Welcome to the Admin Dashboard.</p> 

 

        <a href="admin_dashboard.php">Go to Admin Dashboard</a> 

 

    <?php elseif ($_SESSION["role"] === "student"): ?> 

 

        <h3>Student Dashboard</h3> 

 

        <p>Welcome to the Student Dashboard.</p> 

 

        <a href="student_dashboard.php">Go to Student Dashboard</a> 

 
        <p>
    Last Login:
    <strong>
        <?php
        require_once "db.php";

        $stmt = $conn->prepare(
            "SELECT last_login FROM users WHERE id = ?"
        );

        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo !empty($row["last_login"])
            ? htmlspecialchars($row["last_login"])
            : "First login";

        $stmt->close();
        $conn->close();
        ?>
    </strong>
</p>
    <?php endif; ?> 

 

    <br><br> 

 

    <a href="logout.php">Logout</a> 

 

</div> 

 

</body> 

</html>