<?php
// includes/auth.php : session, RBAC check, audit logging, dynamic menu
session_start();
require_once __DIR__ . '/db.php';

/* ---------- Menu definition: [label, link, icon, roles allowed to see it] ---------- */
const MENU = [
    ['Dashboard',    'dashboard.php',  'bi-speedometer2',  ['admin']],
    ['Students',     'students.php',   'bi-people',        ['admin', 'faculty']],
    ['Events',       'events.php',     'bi-calendar-event',['admin', 'faculty', 'student']],
    ['Manage Users', 'users.php',      'bi-person-gear',   ['admin']],
    ['Audit Logs',   'audit_logs.php', 'bi-journal-text',  ['admin']],
];

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

/* ---------- RBAC check: only the listed roles may continue ---------- */
function require_role(array $roles): void {
    require_login();
    if (!in_array(current_user()['role'], $roles, true)) {
        log_action('ACCESS_DENIED', 'Tried to open ' . basename($_SERVER['PHP_SELF']));
        http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
}

/* ---------- Audit log writer ---------- */
function log_action(string $action, string $details = ''): void {
    global $pdo;
    $uid = current_user()['id'] ?? null;
    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$uid, $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
}

/* ---------- Menu items visible for the logged-in role ---------- */
function menu_for_role(string $role): array {
    return array_values(array_filter(MENU, fn($m) => in_array($role, $m[3], true)));
}

/* ---------- CSRF helpers ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die('Invalid CSRF token.');
    }
}

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
