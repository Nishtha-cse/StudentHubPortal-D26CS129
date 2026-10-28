<?php
// includes/header.php : Bootstrap layout + dynamic navbar (menu depends on role)
require_once __DIR__ . '/auth.php';
$user    = current_user();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Admin Dashboard') ?> | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:#f4f6fb; }
    .stat-card { border:0; border-radius:14px; color:#fff; }
    .stat-card .bi { font-size:2.2rem; opacity:.85; }
    .table thead th { background:#212529; color:#fff; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="#"><i class="bi bi-shield-lock"></i> Admin Panel</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <?php if ($user): foreach (menu_for_role($user['role']) as [$label, $link, $icon]): ?>
          <li class="nav-item">
            <a class="nav-link <?= $current === $link ? 'active fw-semibold' : '' ?>" href="<?= $link ?>">
              <i class="bi <?= $icon ?>"></i> <?= $label ?>
            </a>
          </li>
        <?php endforeach; endif; ?>
      </ul>
      <?php if ($user): ?>
        <span class="navbar-text me-3">
          <?= e($user['name']) ?>
          <span class="badge bg-info text-dark text-uppercase"><?= e($user['role']) ?></span>
        </span>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<main class="container py-4">
