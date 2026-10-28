<?php
// users.php : ADMIN ONLY
require_once __DIR__ . '/includes/auth.php';
require_role(['admin']);

$users = $pdo->query(
    'SELECT u.id, u.name, u.email, r.name AS role, u.created_at
     FROM users u JOIN roles r ON r.id = u.role_id ORDER BY u.id'
)->fetchAll();

log_action('VIEW_USERS', 'Admin opened user list');

$pageTitle = 'Manage Users';
require __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Users <span class="badge bg-secondary"><?= count($users) ?></span></h3>
<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-striped mb-0">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= e($u['name']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><span class="badge bg-dark text-uppercase"><?= e($u['role']) ?></span></td>
          <td><?= e($u['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
