<?php 

session_start(); 

// Session timeout: 5 minutes 
$timeout = 30; 

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
