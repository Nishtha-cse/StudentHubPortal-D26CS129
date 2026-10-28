<?php
// audit_logs.php : ADMIN ONLY. Shows recorded actions (proof for the practical)
require_once __DIR__ . '/includes/auth.php';
require_role(['admin']);

$logs = $pdo->query(
    'SELECT a.id, COALESCE(u.name, "Unknown") AS user_name, a.action, a.details, a.ip_address, a.created_at
     FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id
     ORDER BY a.id DESC LIMIT 100'
)->fetchAll();

$badge = [
    'LOGIN' => 'success', 'LOGOUT' => 'secondary', 'LOGIN_FAILED' => 'warning',
    'ACCESS_DENIED' => 'danger', 'ADD_EVENT' => 'primary', 'DELETE_EVENT' => 'danger',
    'VIEW_DASHBOARD' => 'info', 'VIEW_USERS' => 'info',
];

$pageTitle = 'Audit Logs';
require __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Audit Logs <small class="text-muted fs-6">(latest 100)</small></h3>
<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle mb-0">
      <thead><tr><th>ID</th><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>Time</th></tr></thead>
      <tbody>
      <?php foreach ($logs as $l): ?>
        <tr>
          <td><?= $l['id'] ?></td>
          <td><?= e($l['user_name']) ?></td>
          <td><span class="badge bg-<?= $badge[$l['action']] ?? 'dark' ?>"><?= e($l['action']) ?></span></td>
          <td><?= e($l['details']) ?></td>
          <td><?= e($l['ip_address']) ?></td>
          <td><?= e($l['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
