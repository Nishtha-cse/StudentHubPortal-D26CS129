<?php
// logout.php
require_once __DIR__ . '/includes/auth.php';
if (current_user()) {
    log_action('LOGOUT', 'User logged out');
}
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
